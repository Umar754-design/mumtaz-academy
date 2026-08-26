import '../mumtaz.css';
import { useState } from 'react';

const quizzes = [
  {
    id: 1,
    title: 'Tajweed Rules – Beginner',
    subject: 'Quran',
    level: 'Beginner',
    questions: 10,
    time: '10 min',
    attempts: '3,200+',
    color: '#1a5c3e',
    questions_data: [
      {
        q: 'What is the meaning of Tajweed?',
        options: ['To read fast', 'To beautify and perfect', 'To memorise', 'To translate'],
        answer: 1,
      },
      {
        q: 'Which letter is a Shamsiyya (solar) letter?',
        options: ['ب', 'ت', 'م', 'ه'],
        answer: 1,
      },
      {
        q: 'What is Ghunnah?',
        options: ['A type of stop', 'A nasal sound', 'A long vowel', 'A pause'],
        answer: 1,
      },
    ],
  },
  {
    id: 2,
    title: 'Arabic Alphabet & Vowels',
    subject: 'Arabic',
    level: 'Beginner',
    questions: 15,
    time: '12 min',
    attempts: '4,100+',
    color: '#1b3a6a',
    questions_data: [],
  },
  {
    id: 3,
    title: 'Pillars of Islam & Iman',
    subject: 'Islamic Studies',
    level: 'Beginner',
    questions: 12,
    time: '10 min',
    attempts: '5,800+',
    color: '#5c3500',
    questions_data: [],
  },
  {
    id: 4,
    title: 'Seerah of the Prophet ﷺ',
    subject: 'Islamic Studies',
    level: 'Intermediate',
    questions: 20,
    time: '20 min',
    attempts: '2,700+',
    color: '#3a5c1a',
    questions_data: [],
  },
  {
    id: 5,
    title: 'Nahw Basics – Nouns & Verbs',
    subject: 'Arabic',
    level: 'Intermediate',
    questions: 18,
    time: '15 min',
    attempts: '1,900+',
    color: '#2d1a5c',
    questions_data: [],
  },
  {
    id: 6,
    title: 'Hadith 40 Nawawi',
    subject: 'Islamic Studies',
    level: 'Advanced',
    questions: 25,
    time: '25 min',
    attempts: '1,400+',
    color: '#5c1a2d',
    questions_data: [],
  },
];

const levelColors: Record<string, string> = {
  Beginner: '#1a5c3e', Intermediate: '#3b4a8c', Advanced: '#7a2020',
};

type QuizState = { quizId: number; current: number; selected: (number | null)[]; done: boolean };

export default function McqTests() {
  const [active, setActive] = useState<QuizState | null>(null);

  function startQuiz(id: number) {
    const q = quizzes.find(q => q.id === id)!;
    if (!q.questions_data.length) return;
    setActive({ quizId: id, current: 0, selected: new Array(q.questions_data.length).fill(null), done: false });
  }

  function pick(optIdx: number) {
    if (!active || active.done) return;
    setActive(a => {
      if (!a) return a;
      const sel = [...a.selected];
      sel[a.current] = optIdx;
      return { ...a, selected: sel };
    });
  }

  function next() {
    if (!active) return;
    const quiz = quizzes.find(q => q.id === active.quizId)!;
    if (active.current < quiz.questions_data.length - 1) {
      setActive(a => a ? { ...a, current: a.current + 1 } : a);
    } else {
      setActive(a => a ? { ...a, done: true } : a);
    }
  }

  if (active) {
    const quiz = quizzes.find(q => q.id === active.quizId)!;
    const qdata = quiz.questions_data;

    if (active.done) {
      const score = active.selected.filter((s, i) => s === qdata[i].answer).length;
      const pct = Math.round((score / qdata.length) * 100);
      return (
        <div style={{ minHeight: '100vh', background: '#f7f7f4', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 24px' }}>
          <div style={{ background: '#fff', borderRadius: '16px', padding: '48px 40px', maxWidth: '480px', width: '100%', textAlign: 'center', boxShadow: '0 8px 40px rgba(0,0,0,0.1)' }}>
            <div style={{ fontSize: '56px', fontWeight: 800, color: pct >= 70 ? '#1a5c3e' : '#c0392b', marginBottom: '8px' }}>{pct}%</div>
            <div style={{ fontSize: '20px', fontWeight: 700, marginBottom: '8px' }}>{pct >= 70 ? '🎉 Well Done!' : '📚 Keep Practising'}</div>
            <div style={{ color: '#555', fontSize: '14px', marginBottom: '28px' }}>You scored {score} out of {qdata.length} questions.</div>
            <div style={{ display: 'flex', gap: '12px', justifyContent: 'center' }}>
              <button className="ma-btn-dark" onClick={() => startQuiz(active.quizId)} style={{ border: 'none', cursor: 'pointer' }}>Try Again</button>
              <button className="ma-filter-pill" onClick={() => setActive(null)} style={{ cursor: 'pointer' }}>All Tests</button>
            </div>
          </div>
        </div>
      );
    }

    const current = qdata[active.current];
    const progress = ((active.current) / qdata.length) * 100;

    return (
      <div style={{ minHeight: '100vh', background: '#f7f7f4', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 24px' }}>
        <div style={{ background: '#fff', borderRadius: '16px', padding: '36px', maxWidth: '560px', width: '100%', boxShadow: '0 8px 40px rgba(0,0,0,0.08)' }}>
          {/* Header */}
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
            <div style={{ fontSize: '13px', fontWeight: 700, color: '#0b2b2b' }}>{quiz.title}</div>
            <div style={{ fontSize: '12.5px', color: '#999' }}>Q {active.current + 1} / {qdata.length}</div>
          </div>
          {/* Progress */}
          <div style={{ height: '4px', background: '#eee', borderRadius: '4px', marginBottom: '28px', overflow: 'hidden' }}>
            <div style={{ height: '100%', background: '#c9a227', borderRadius: '4px', width: `${progress}%`, transition: 'width 0.3s' }} />
          </div>
          {/* Question */}
          <p style={{ fontSize: '17px', fontWeight: 700, color: '#111', marginBottom: '22px', lineHeight: '1.5' }}>{current.q}</p>
          {/* Options */}
          <div style={{ display: 'flex', flexDirection: 'column', gap: '10px', marginBottom: '28px' }}>
            {current.options.map((opt, i) => {
              const sel = active.selected[active.current];
              return (
                <button key={i} onClick={() => pick(i)}
                  style={{
                    padding: '13px 16px', borderRadius: '10px', border: '2px solid',
                    borderColor: sel === i ? '#0b2b2b' : '#e0e0e0',
                    background: sel === i ? 'rgba(11,43,43,0.05)' : '#fff',
                    cursor: 'pointer', textAlign: 'left', fontSize: '14.5px', color: '#222',
                    fontFamily: 'inherit', transition: 'all 0.15s', fontWeight: sel === i ? 600 : 400,
                  }}
                >
                  <span style={{ marginRight: '10px', color: '#bbb', fontWeight: 600 }}>{String.fromCharCode(65 + i)}.</span>
                  {opt}
                </button>
              );
            })}
          </div>
          <div style={{ display: 'flex', gap: '12px' }}>
            <button className="ma-filter-pill" onClick={() => setActive(null)} style={{ cursor: 'pointer' }}>Exit</button>
            <button className="ma-auth-submit" style={{ flex: 1 }}
              onClick={next}
              disabled={active.selected[active.current] === null}
            >
              {active.current < qdata.length - 1 ? 'Next Question →' : 'Finish Quiz'}
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div>
      <section className="ma-page-banner">
        <h1>MCQ Tests</h1>
        <p>Assess your Islamic knowledge with interactive quizzes</p>
      </section>

      {/* Stats */}
      <section style={{ background: '#f7f7f4', padding: '28px 24px', borderBottom: '1px solid #eee' }}>
        <div style={{ maxWidth: '900px', margin: '0 auto', display: 'flex', justifyContent: 'center', gap: '48px', flexWrap: 'wrap' }}>
          {[
            { num: '30+', label: 'Practice Tests' },
            { num: '500+', label: 'Questions' },
            { num: '19K+', label: 'Attempts' },
            { num: 'Free', label: 'No Sign-up Required' },
          ].map(s => (
            <div key={s.label} style={{ textAlign: 'center' }}>
              <div style={{ fontSize: '26px', fontWeight: 800, color: '#c9a227' }}>{s.num}</div>
              <div style={{ fontSize: '12.5px', color: '#666', marginTop: '2px' }}>{s.label}</div>
            </div>
          ))}
        </div>
      </section>

      {/* Quiz grid */}
      <section style={{ padding: '50px 24px', maxWidth: '1100px', margin: '0 auto' }}>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '24px' }}>
          {quizzes.map(quiz => (
            <div key={quiz.id} style={{ background: '#fff', borderRadius: '14px', overflow: 'hidden', boxShadow: '0 2px 12px rgba(0,0,0,0.06)', border: '1.5px solid #eee' }}>
              <div style={{ background: quiz.color, padding: '20px 22px', display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                <div>
                  <div style={{ fontSize: '11px', color: 'rgba(255,255,255,0.5)', fontWeight: 600, textTransform: 'uppercase', letterSpacing: '0.5px', marginBottom: '4px' }}>{quiz.subject}</div>
                  <h3 style={{ fontSize: '16px', fontWeight: 700, color: '#fff', lineHeight: '1.3' }}>{quiz.title}</h3>
                </div>
                <span style={{ background: levelColors[quiz.level], color: '#fff', fontSize: '10.5px', fontWeight: 600, padding: '3px 10px', borderRadius: '20px', flexShrink: 0, marginLeft: '8px' }}>{quiz.level}</span>
              </div>
              <div style={{ padding: '18px 22px' }}>
                <div style={{ display: 'flex', gap: '20px', marginBottom: '16px' }}>
                  {[
                    { icon: '❓', label: `${quiz.questions} Questions` },
                    { icon: '⏱️', label: quiz.time },
                    { icon: '👥', label: `${quiz.attempts} attempts` },
                  ].map(m => (
                    <div key={m.label} style={{ fontSize: '12px', color: '#666' }}>
                      <span style={{ marginRight: '4px' }}>{m.icon}</span>{m.label}
                    </div>
                  ))}
                </div>
                <button
                  className={quiz.questions_data.length ? 'ma-enroll-btn' : 'ma-filter-pill'}
                  style={{ width: '100%', textAlign: 'center', cursor: quiz.questions_data.length ? 'pointer' : 'default', border: 'none', padding: '10px', borderRadius: '8px', fontFamily: 'inherit', fontSize: '14px' }}
                  onClick={() => startQuiz(quiz.id)}
                  disabled={!quiz.questions_data.length}
                >
                  {quiz.questions_data.length ? 'Start Test →' : 'Coming Soon'}
                </button>
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}
