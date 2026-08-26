import '../mumtaz.css';

const courses = [
  {
    title: 'Quran Learning',
    desc: 'Learn Quran with Tajweed, recitation and understanding. Our expert scholars guide you through proper pronunciation and meaning.',
    icon: '📚',
    bg: 'linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%)',
    label: 'ٱلۡقُرۡءَانُ',
    level: 'Beginner',
    duration: '3 Months',
    students: '1200+',
    tag: 'Quran',
  },
  {
    title: 'Arabic Language',
    desc: 'Learn Arabic reading, writing, grammar and conversation. Master the language of the Quran from foundational to advanced levels.',
    icon: 'ع',
    bg: 'linear-gradient(135deg, #1b3a3a 0%, #0d5050 100%)',
    label: 'اَلۡعَرَبِيَّة',
    level: 'All Levels',
    duration: '6 Months',
    students: '900+',
    tag: 'Arabic',
  },
  {
    title: 'Islamic Studies',
    desc: 'Learn Aqaid, Fiqh, Seerah, Hadith and much more. A comprehensive curriculum covering all essential aspects of Islamic knowledge.',
    icon: '🕌',
    bg: 'linear-gradient(135deg, #2d1a00 0%, #5c3500 100%)',
    label: '',
    level: 'Intermediate',
    duration: '4 Months',
    students: '800+',
    tag: 'Islamic',
  },
  {
    title: 'Tajweed Mastery',
    desc: 'Perfect your Quran recitation with in-depth Tajweed rules. Learn the rules of elongation, stops, and proper letter articulation.',
    icon: '🎙️',
    bg: 'linear-gradient(135deg, #1a2d1a 0%, #2d5a2d 100%)',
    label: 'تَجْوِيد',
    level: 'Intermediate',
    duration: '2 Months',
    students: '600+',
    tag: 'Quran',
  },
  {
    title: 'Hadith Sciences',
    desc: 'Study the sayings and traditions of the Prophet ﷺ. Learn chain of narration, authenticity, and application in daily life.',
    icon: '📜',
    bg: 'linear-gradient(135deg, #1a1a2d 0%, #2d2d5a 100%)',
    label: 'حَدِيث',
    level: 'Advanced',
    duration: '5 Months',
    students: '450+',
    tag: 'Islamic',
  },
  {
    title: 'Arabic Grammar (Nahw)',
    desc: 'Master Arabic grammar through classical texts. Understand sentence structures, verb conjugations, and grammatical rules thoroughly.',
    icon: '✍️',
    bg: 'linear-gradient(135deg, #2d1a2d 0%, #5a2d5a 100%)',
    label: 'نَحْو',
    level: 'Advanced',
    duration: '4 Months',
    students: '380+',
    tag: 'Arabic',
  },
];

const filters = ['All', 'Quran', 'Arabic', 'Islamic'];

export default function Courses() {
  return (
    <div>
      {/* Page Banner */}
      <section className="ma-page-banner">
        <h1>Our Courses</h1>
        <p>Quality Islamic Education for Everyone — Free of Cost</p>
      </section>

      <section className="ma-courses" style={{ paddingTop: '50px' }}>
        {/* Filter pills */}
        <div style={{ display: 'flex', justifyContent: 'center', gap: '10px', marginBottom: '36px', flexWrap: 'wrap' }}>
          {filters.map(f => (
            <button key={f} className={f === 'All' ? 'ma-filter-pill active' : 'ma-filter-pill'}>{f}</button>
          ))}
        </div>
        <div className="ma-courses-grid" style={{ maxWidth: '1100px', margin: '0 auto 32px' }}>
          {courses.map(c => (
            <div className="ma-course-card" key={c.title}>
              <div className="ma-course-img" style={{ background: c.bg }}>
                <div className="ma-course-img-text">{c.label}</div>
                <div className="ma-course-img-icon">{c.icon}</div>
              </div>
              <div className="ma-course-body">
                <div style={{ display: 'flex', gap: '8px', marginBottom: '8px', flexWrap: 'wrap' }}>
                  <span className="ma-pill">{c.level}</span>
                  <span className="ma-pill">{c.duration}</span>
                  <span className="ma-pill">{c.students} Students</span>
                </div>
                <h3>{c.title}</h3>
                <p>{c.desc}</p>
                <div className="ma-course-footer">
                  <span className="ma-free-badge">Free Course</span>
                  <a href="#" className="ma-enroll-btn">Enroll Now →</a>
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}
