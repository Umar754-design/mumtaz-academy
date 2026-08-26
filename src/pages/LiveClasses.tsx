import '../mumtaz.css';

const schedule = [
  { day: 'Monday', time: '9:00 AM – 10:00 AM', subject: 'Quran Recitation', teacher: 'Ustadh Abdul Rahman', level: 'Beginner', slots: 12 },
  { day: 'Monday', time: '7:00 PM – 8:00 PM', subject: 'Arabic Grammar', teacher: 'Ustadha Fatima Khan', level: 'Intermediate', slots: 8 },
  { day: 'Tuesday', time: '10:00 AM – 11:00 AM', subject: 'Islamic Studies', teacher: 'Maulana Siddiqui', level: 'All Levels', slots: 15 },
  { day: 'Tuesday', time: '8:00 PM – 9:00 PM', subject: 'Tajweed Rules', teacher: 'Hafiz Muhammad Ali', level: 'Beginner', slots: 10 },
  { day: 'Wednesday', time: '9:00 AM – 10:00 AM', subject: 'Hadith Sciences', teacher: 'Dr. Khalid Mehmood', level: 'Advanced', slots: 6 },
  { day: 'Wednesday', time: '6:00 PM – 7:00 PM', subject: 'Quran with Translation', teacher: 'Ustadh Ibrahim Shah', level: 'All Levels', slots: 14 },
  { day: 'Thursday', time: '10:00 AM – 11:00 AM', subject: 'Arabic Conversation', teacher: 'Ustadha Amina', level: 'Intermediate', slots: 9 },
  { day: 'Thursday', time: '7:00 PM – 8:00 PM', subject: 'Islamic Fiqh', teacher: 'Mufti Saeed Ahmad', level: 'Advanced', slots: 7 },
  { day: 'Friday', time: '9:00 AM – 10:00 AM', subject: 'Seerah of the Prophet ﷺ', teacher: 'Ustadh Yusuf Ali', level: 'All Levels', slots: 20 },
  { day: 'Saturday', time: '11:00 AM – 12:00 PM', subject: 'Tajweed Mastery', teacher: 'Hafiza Zainab', level: 'All Levels', slots: 16 },
  { day: 'Saturday', time: '5:00 PM – 6:00 PM', subject: 'Quran Memorization', teacher: 'Hafiz Abdul Basit', level: 'All Levels', slots: 12 },
  { day: 'Sunday', time: '10:00 AM – 12:00 PM', subject: 'Weekend Islamic Studies', teacher: 'Panel of Scholars', level: 'All Levels', slots: 30 },
];

const levelColors: Record<string, string> = {
  'Beginner': '#1a5c3e',
  'Intermediate': '#3b4a8c',
  'Advanced': '#7a2020',
  'All Levels': '#4a6a1a',
};

export default function LiveClasses() {
  return (
    <div>
      <section className="ma-page-banner">
        <h1>Live Classes</h1>
        <p>Interactive sessions with expert Islamic scholars — join from anywhere</p>
      </section>

      {/* Stats row */}
      <section style={{ background: '#f7f7f4', padding: '30px 24px', borderBottom: '1px solid #e8e8e8' }}>
        <div style={{ maxWidth: '900px', margin: '0 auto', display: 'flex', gap: '40px', justifyContent: 'center', flexWrap: 'wrap' }}>
          {[
            { num: '100+', label: 'Live Classes / Month' },
            { num: '20+', label: 'Expert Teachers' },
            { num: '5000+', label: 'Active Students' },
            { num: 'Free', label: 'No Charges Ever' },
          ].map(s => (
            <div key={s.label} style={{ textAlign: 'center' }}>
              <div style={{ fontSize: '28px', fontWeight: 800, color: '#c9a227' }}>{s.num}</div>
              <div style={{ fontSize: '13px', color: '#555', marginTop: '4px' }}>{s.label}</div>
            </div>
          ))}
        </div>
      </section>

      <section style={{ padding: '50px 24px', maxWidth: '1100px', margin: '0 auto' }}>
        <h2 style={{ textAlign: 'center', fontWeight: 800, fontSize: '24px', marginBottom: '8px' }}>Weekly Schedule</h2>
        <p style={{ textAlign: 'center', color: '#666', marginBottom: '36px', fontSize: '14px' }}>All times are in IST (Indian Standard Time)</p>
        <div style={{ overflowX: 'auto' }}>
          <table className="ma-schedule-table">
            <thead>
              <tr>
                <th>Day</th>
                <th>Time</th>
                <th>Subject</th>
                <th>Teacher</th>
                <th>Level</th>
                <th>Available Slots</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {schedule.map((c, i) => (
                <tr key={i}>
                  <td><strong>{c.day}</strong></td>
                  <td style={{ whiteSpace: 'nowrap', color: '#555', fontSize: '13px' }}>{c.time}</td>
                  <td style={{ fontWeight: 600 }}>{c.subject}</td>
                  <td style={{ color: '#555', fontSize: '13px' }}>{c.teacher}</td>
                  <td>
                    <span className="ma-level-badge" style={{ background: levelColors[c.level] || '#333' }}>
                      {c.level}
                    </span>
                  </td>
                  <td style={{ textAlign: 'center' }}>
                    <span style={{ color: c.slots < 8 ? '#c0392b' : '#27ae60', fontWeight: 600, fontSize: '13px' }}>
                      {c.slots} left
                    </span>
                  </td>
                  <td>
                    <a href="#" className="ma-enroll-btn" style={{ fontSize: '12px', padding: '5px 12px' }}>Join →</a>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* How it works */}
      <section style={{ background: '#0b2b2b', padding: '60px 24px' }}>
        <div style={{ maxWidth: '900px', margin: '0 auto', textAlign: 'center' }}>
          <h2 style={{ color: '#ffffff', fontWeight: 800, fontSize: '26px', marginBottom: '8px' }}>How Live Classes Work</h2>
          <p style={{ color: 'rgba(255,255,255,0.6)', marginBottom: '40px', fontSize: '14px' }}>Simple steps to start your learning journey</p>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '24px' }}>
            {[
              { step: '01', title: 'Register Free', desc: 'Create your free account in under 2 minutes.' },
              { step: '02', title: 'Pick a Class', desc: 'Browse the schedule and choose a session that fits your time.' },
              { step: '03', title: 'Join the Link', desc: 'Click the Join link and enter the live Zoom/Google Meet session.' },
              { step: '04', title: 'Learn & Grow', desc: 'Interact with the teacher, ask questions, and get your certificate.' },
            ].map(s => (
              <div key={s.step} style={{ background: 'rgba(255,255,255,0.05)', border: '1px solid rgba(201,162,39,0.2)', borderRadius: '10px', padding: '24px 20px' }}>
                <div style={{ fontSize: '28px', fontWeight: 800, color: '#c9a227', marginBottom: '10px' }}>{s.step}</div>
                <div style={{ fontSize: '15px', fontWeight: 700, color: '#fff', marginBottom: '8px' }}>{s.title}</div>
                <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.6)', lineHeight: '1.6' }}>{s.desc}</div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
