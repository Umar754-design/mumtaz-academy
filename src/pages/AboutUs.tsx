import '../mumtaz.css';

export default function AboutUs() {
  return (
    <div>
      <section className="ma-page-banner">
        <h1>About Us</h1>
        <p>Bringing quality Islamic education to every home, everywhere</p>
      </section>

      {/* Mission */}
      <section style={{ padding: '70px 24px', maxWidth: '1000px', margin: '0 auto', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '60px', alignItems: 'center' }}>
        <div>
          <h2 style={{ fontSize: '30px', fontWeight: 800, color: '#0b2b2b', marginBottom: '16px', lineHeight: '1.3' }}>
            Our <span style={{ color: '#c9a227' }}>Mission</span>
          </h2>
          <p style={{ color: '#444', lineHeight: '1.8', fontSize: '15px', marginBottom: '16px' }}>
            Mumtaz Academy was founded with a single purpose: to make authentic Islamic education accessible to every Muslim, regardless of where they live or their financial situation.
          </p>
          <p style={{ color: '#444', lineHeight: '1.8', fontSize: '15px', marginBottom: '16px' }}>
            We believe that knowledge of the Quran, Arabic, and Islamic sciences is a right of every Muslim — not a privilege. That is why every course, every live class, and every certificate on our platform is completely free.
          </p>
          <p style={{ color: '#444', lineHeight: '1.8', fontSize: '15px' }}>
            Our team of qualified scholars and educators has crafted a structured curriculum that takes students from complete beginners to confident practitioners of Islamic knowledge.
          </p>
        </div>
        <div style={{ background: 'linear-gradient(135deg, #0b2b2b 0%, #1a5c3e 100%)', borderRadius: '16px', padding: '40px', display: 'flex', flexDirection: 'column', gap: '24px' }}>
          {[
            { label: 'Founded', value: '2020' },
            { label: 'Students Taught', value: '5,000+' },
            { label: 'Courses Available', value: '50+' },
            { label: 'Countries Reached', value: '30+' },
          ].map(s => (
            <div key={s.label} style={{ display: 'flex', justifyContent: 'space-between', borderBottom: '1px solid rgba(201,162,39,0.2)', paddingBottom: '16px' }}>
              <span style={{ color: 'rgba(255,255,255,0.6)', fontSize: '14px' }}>{s.label}</span>
              <span style={{ color: '#c9a227', fontWeight: 800, fontSize: '18px' }}>{s.value}</span>
            </div>
          ))}
        </div>
      </section>

      {/* Values */}
      <section style={{ background: '#f7f7f4', padding: '60px 24px' }}>
        <div style={{ maxWidth: '1000px', margin: '0 auto' }}>
          <h2 style={{ textAlign: 'center', fontSize: '28px', fontWeight: 800, marginBottom: '8px' }}>Our Core Values</h2>
          <p style={{ textAlign: 'center', color: '#666', marginBottom: '40px', fontSize: '14px' }}>The principles that guide everything we do</p>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: '24px' }}>
            {[
              { icon: '📖', title: 'Authenticity', desc: 'Every lesson is grounded in authentic Quran and Sunnah, verified by qualified scholars.' },
              { icon: '🌍', title: 'Accessibility', desc: 'Free education for every Muslim worldwide. No barriers, no fees, no exceptions.' },
              { icon: '🎓', title: 'Excellence', desc: 'We maintain the highest standards of teaching and curriculum development.' },
              { icon: '🤝', title: 'Community', desc: 'Building a global community of learners who support and inspire each other.' },
            ].map(v => (
              <div key={v.title} style={{ background: '#fff', borderRadius: '12px', padding: '28px', boxShadow: '0 2px 12px rgba(0,0,0,0.06)', textAlign: 'center' }}>
                <div style={{ fontSize: '36px', marginBottom: '12px' }}>{v.icon}</div>
                <h3 style={{ fontSize: '16px', fontWeight: 700, marginBottom: '10px', color: '#0b2b2b' }}>{v.title}</h3>
                <p style={{ fontSize: '13.5px', color: '#555', lineHeight: '1.6' }}>{v.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Story */}
      <section style={{ padding: '70px 24px', background: '#0b2b2b' }}>
        <div style={{ maxWidth: '720px', margin: '0 auto', textAlign: 'center' }}>
          <h2 style={{ fontSize: '28px', fontWeight: 800, color: '#ffffff', marginBottom: '20px' }}>Our Story</h2>
          <p style={{ color: 'rgba(255,255,255,0.7)', lineHeight: '1.9', fontSize: '15px', marginBottom: '16px' }}>
            Mumtaz Academy started in a small room in Dhanbad, Jharkhand, where a group of passionate Islamic scholars began teaching Quran to local children — free of cost. Word spread quickly, and soon students from across India were requesting online access.
          </p>
          <p style={{ color: 'rgba(255,255,255,0.7)', lineHeight: '1.9', fontSize: '15px', marginBottom: '16px' }}>
            In 2020, we launched our online platform, and within a year we had students from 15 countries. Today, Mumtaz Academy is a thriving digital Islamic institution serving thousands of students worldwide with live interactive classes, recorded lessons, and internationally recognized certificates.
          </p>
          <p style={{ color: 'rgba(255,255,255,0.7)', lineHeight: '1.9', fontSize: '15px' }}>
            Our name "Mumtaz" (ممتاز) means "distinguished" or "excellent" in Arabic — and that is exactly the standard we hold ourselves to in every class we deliver.
          </p>
        </div>
      </section>
    </div>
  );
}
