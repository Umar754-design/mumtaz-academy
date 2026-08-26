import '../mumtaz.css';

const teachers = [
  {
    name: 'Ustadh Abdul Rahman',
    title: 'Quran & Tajweed Specialist',
    bio: 'Hafiz of the Quran with 15 years of teaching experience. Trained under renowned scholars in Madinah. Specialises in Tajweed and Hifz programs.',
    subjects: ['Quran Recitation', 'Tajweed', 'Hifz Program'],
    students: '1,200+',
    exp: '15 years',
    initials: 'AR',
    color: '#1a5c3e',
  },
  {
    name: 'Ustadha Fatima Khan',
    title: 'Arabic Language Expert',
    bio: 'Graduate from Al-Azhar University, Cairo. Has authored three textbooks on Arabic grammar used by students across South Asia.',
    subjects: ['Arabic Grammar (Nahw)', 'Arabic Conversation', 'Classical Arabic'],
    students: '900+',
    exp: '12 years',
    initials: 'FK',
    color: '#1b3a6a',
  },
  {
    name: 'Maulana Siddiqui',
    title: 'Islamic Studies Scholar',
    bio: 'Dars-e-Nizami graduate with a Masters in Islamic Jurisprudence. Has taught at seminaries in Pakistan and the UK before joining Mumtaz Academy.',
    subjects: ['Islamic Studies', 'Fiqh', 'Aqeedah'],
    students: '800+',
    exp: '20 years',
    initials: 'MS',
    color: '#5c3a00',
  },
  {
    name: 'Dr. Khalid Mehmood',
    title: 'Hadith Sciences Expert',
    bio: 'PhD in Hadith Sciences from the International Islamic University, Islamabad. Author of several academic papers on Hadith authentication methodology.',
    subjects: ['Hadith Sciences', 'Usool al-Hadith', 'Seerah'],
    students: '450+',
    exp: '18 years',
    initials: 'KM',
    color: '#3a1a5c',
  },
  {
    name: 'Hafiz Muhammad Ali',
    title: 'Tajweed & Hifz Teacher',
    bio: 'Completed memorisation of the Quran at age 14. Has helped over 500 students achieve Hifz with his patient and structured methodology.',
    subjects: ['Tajweed Mastery', 'Hifz Program', 'Quran Recitation'],
    students: '600+',
    exp: '10 years',
    initials: 'MA',
    color: '#1a4a2a',
  },
  {
    name: 'Ustadha Amina',
    title: 'Arabic Communication Coach',
    bio: 'Native Arabic speaker from Egypt with a degree in Arabic Literature. Specialises in conversational Arabic and helping students gain fluency.',
    subjects: ['Arabic Conversation', 'Modern Standard Arabic', 'Arabic Literature'],
    students: '700+',
    exp: '8 years',
    initials: 'AM',
    color: '#4a1a1a',
  },
];

export default function Teachers() {
  return (
    <div>
      <section className="ma-page-banner">
        <h1>Our Teachers</h1>
        <p>Qualified and experienced Islamic scholars dedicated to your learning</p>
      </section>

      <section style={{ padding: '60px 24px', background: '#f7f7f4' }}>
        <div style={{ maxWidth: '1100px', margin: '0 auto' }}>
          <div style={{ textAlign: 'center', marginBottom: '48px' }}>
            <h2 style={{ fontSize: '28px', fontWeight: 800, color: '#111', marginBottom: '8px' }}>Meet the Scholars</h2>
            <p style={{ color: '#666', fontSize: '14px' }}>Every teacher at Mumtaz Academy holds formal Islamic qualifications</p>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '28px' }}>
            {teachers.map(t => (
              <div key={t.name} style={{ background: '#fff', borderRadius: '14px', overflow: 'hidden', boxShadow: '0 2px 14px rgba(0,0,0,0.07)', transition: 'transform 0.2s' }}>
                {/* Header */}
                <div style={{ background: t.color, padding: '28px 24px', display: 'flex', alignItems: 'center', gap: '16px' }}>
                  <div style={{
                    width: '64px', height: '64px', borderRadius: '50%',
                    background: 'rgba(201,162,39,0.25)', border: '2px solid #c9a227',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    fontSize: '22px', fontWeight: 800, color: '#c9a227', flexShrink: 0
                  }}>
                    {t.initials}
                  </div>
                  <div>
                    <div style={{ fontWeight: 700, fontSize: '17px', color: '#fff' }}>{t.name}</div>
                    <div style={{ fontSize: '12.5px', color: 'rgba(255,255,255,0.65)', marginTop: '2px' }}>{t.title}</div>
                    <div style={{ display: 'flex', gap: '12px', marginTop: '6px' }}>
                      <span style={{ fontSize: '11.5px', color: '#c9a227', fontWeight: 600 }}>👥 {t.students}</span>
                      <span style={{ fontSize: '11.5px', color: '#c9a227', fontWeight: 600 }}>🕐 {t.exp}</span>
                    </div>
                  </div>
                </div>
                {/* Body */}
                <div style={{ padding: '20px 24px' }}>
                  <p style={{ fontSize: '13.5px', color: '#555', lineHeight: '1.7', marginBottom: '16px' }}>{t.bio}</p>
                  <div style={{ fontSize: '12px', color: '#888', marginBottom: '8px', fontWeight: 600, textTransform: 'uppercase', letterSpacing: '0.5px' }}>Subjects</div>
                  <div style={{ display: 'flex', flexWrap: 'wrap', gap: '6px' }}>
                    {t.subjects.map(s => (
                      <span key={s} style={{
                        background: '#f0f7f4', color: '#0b2b2b', fontSize: '12px',
                        padding: '3px 10px', borderRadius: '20px', border: '1px solid #c9e8d0', fontWeight: 500
                      }}>{s}</span>
                    ))}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Join us */}
      <section style={{ background: '#0b2b2b', padding: '60px 24px', textAlign: 'center' }}>
        <h2 style={{ color: '#fff', fontSize: '26px', fontWeight: 800, marginBottom: '12px' }}>Are You a Scholar?</h2>
        <p style={{ color: 'rgba(255,255,255,0.65)', fontSize: '15px', marginBottom: '28px', maxWidth: '500px', margin: '0 auto 28px' }}>
          We are always looking for qualified Islamic scholars to join our teaching team and help students worldwide.
        </p>
        <a href="#" className="ma-btn-gold" style={{ fontSize: '15px', padding: '12px 28px', borderRadius: '8px' }}>Apply to Teach</a>
      </section>
    </div>
  );
}
