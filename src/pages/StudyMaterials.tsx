import '../mumtaz.css';

const categories = [
  {
    title: 'Quran & Tajweed',
    color: '#0d3b2e',
    accent: '#1a5c3e',
    items: [
      { name: 'Noorani Qaida – Complete PDF', type: 'PDF', size: '2.4 MB', downloads: '8,200+' },
      { name: 'Tajweed Rules – Full Guide', type: 'PDF', size: '1.8 MB', downloads: '6,400+' },
      { name: 'Makharij al-Huroof Chart', type: 'PDF', size: '0.9 MB', downloads: '4,100+' },
      { name: 'Surah Memorisation Worksheet', type: 'PDF', size: '1.2 MB', downloads: '3,800+' },
    ],
  },
  {
    title: 'Arabic Language',
    color: '#1b3a3a',
    accent: '#0d5050',
    items: [
      { name: 'Arabic Alphabet Flash Cards', type: 'PDF', size: '3.1 MB', downloads: '5,500+' },
      { name: 'Nahw Basics – Vocabulary List', type: 'PDF', size: '0.7 MB', downloads: '4,900+' },
      { name: 'Arabic Root Words Reference', type: 'PDF', size: '1.5 MB', downloads: '3,600+' },
      { name: 'Verb Conjugation Tables', type: 'PDF', size: '1.1 MB', downloads: '4,200+' },
    ],
  },
  {
    title: 'Islamic Studies',
    color: '#2d1a00',
    accent: '#5c3500',
    items: [
      { name: 'Pillars of Islam – Study Notes', type: 'PDF', size: '0.8 MB', downloads: '7,100+' },
      { name: 'Aqeedah Summary Sheet', type: 'PDF', size: '0.6 MB', downloads: '5,300+' },
      { name: 'Hadith 40 Nawawi – Booklet', type: 'PDF', size: '1.4 MB', downloads: '6,800+' },
      { name: 'Seerah Timeline Infographic', type: 'PDF', size: '2.0 MB', downloads: '4,700+' },
    ],
  },
  {
    title: 'Fiqh & Worship',
    color: '#1a1a2d',
    accent: '#2d2d5a',
    items: [
      { name: 'Salah Guide – Step by Step', type: 'PDF', size: '1.7 MB', downloads: '9,400+' },
      { name: 'Wudu & Ghusl Reference Card', type: 'PDF', size: '0.5 MB', downloads: '7,200+' },
      { name: 'Duas for Daily Life', type: 'PDF', size: '1.3 MB', downloads: '8,800+' },
      { name: 'Ramadan Ibadah Planner', type: 'PDF', size: '0.9 MB', downloads: '5,600+' },
    ],
  },
];

const featured = [
  { icon: '📖', title: 'Complete Quran PDF', desc: 'High-quality Uthmani script Quran with colour-coded Tajweed marks.', tag: 'Most Downloaded' },
  { icon: '🗂️', title: 'Islamic Studies Pack', desc: 'A bundle of 12 study notes covering Aqeedah, Fiqh, and Seerah.', tag: 'Bundle' },
  { icon: '✍️', title: 'Arabic Writing Practice Sheets', desc: '30 printable worksheets for learning Arabic letters and words.', tag: 'Printable' },
];

export default function StudyMaterials() {
  return (
    <div>
      <section className="ma-page-banner">
        <h1>Study Materials</h1>
        <p>Free downloadable resources for every course — PDFs, worksheets, and references</p>
      </section>

      {/* Stats */}
      <section style={{ background: '#f7f7f4', padding: '28px 24px', borderBottom: '1px solid #eee' }}>
        <div style={{ maxWidth: '900px', margin: '0 auto', display: 'flex', justifyContent: 'center', gap: '48px', flexWrap: 'wrap' }}>
          {[
            { num: '80+', label: 'Resources' },
            { num: '200K+', label: 'Total Downloads' },
            { num: '4', label: 'Categories' },
            { num: 'Free', label: 'Always' },
          ].map(s => (
            <div key={s.label} style={{ textAlign: 'center' }}>
              <div style={{ fontSize: '26px', fontWeight: 800, color: '#c9a227' }}>{s.num}</div>
              <div style={{ fontSize: '12.5px', color: '#666', marginTop: '2px' }}>{s.label}</div>
            </div>
          ))}
        </div>
      </section>

      {/* Featured */}
      <section style={{ padding: '50px 24px 0', maxWidth: '1100px', margin: '0 auto' }}>
        <h2 style={{ fontWeight: 800, fontSize: '22px', marginBottom: '20px' }}>⭐ Featured Resources</h2>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: '20px', marginBottom: '50px' }}>
          {featured.map(f => (
            <div key={f.title} style={{ background: '#0b2b2b', borderRadius: '12px', padding: '24px', position: 'relative', overflow: 'hidden' }}>
              <span style={{ position: 'absolute', top: '12px', right: '12px', background: '#c9a227', color: '#0b2b2b', fontSize: '10.5px', fontWeight: 700, padding: '3px 10px', borderRadius: '20px' }}>{f.tag}</span>
              <div style={{ fontSize: '36px', marginBottom: '12px' }}>{f.icon}</div>
              <h3 style={{ fontSize: '16px', fontWeight: 700, color: '#fff', marginBottom: '8px' }}>{f.title}</h3>
              <p style={{ fontSize: '13px', color: 'rgba(255,255,255,0.6)', lineHeight: '1.6', marginBottom: '16px' }}>{f.desc}</p>
              <button className="ma-download-btn" type="button">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download Free
              </button>
            </div>
          ))}
        </div>
      </section>

      {/* Categories */}
      <section style={{ padding: '0 24px 60px', maxWidth: '1100px', margin: '0 auto' }}>
        {categories.map(cat => (
          <div key={cat.title} style={{ marginBottom: '40px' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '16px' }}>
              <div style={{ width: '4px', height: '24px', background: '#c9a227', borderRadius: '2px' }} />
              <h2 style={{ fontSize: '20px', fontWeight: 800, color: '#111' }}>{cat.title}</h2>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(260px, 1fr))', gap: '14px' }}>
              {cat.items.map(item => (
                <div key={item.name} className="ma-resource-card">
                  <div style={{ display: 'flex', alignItems: 'center', gap: '12px', minWidth: 0 }}>
                    <div style={{ width: '38px', height: '38px', background: `linear-gradient(135deg, ${cat.color}, ${cat.accent})`, borderRadius: '8px', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                      <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div style={{ minWidth: 0 }}>
                      <div style={{ fontSize: '13.5px', fontWeight: 600, color: '#111', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>{item.name}</div>
                      <div style={{ fontSize: '11.5px', color: '#999', marginTop: '2px' }}>{item.type} · {item.size} · {item.downloads} downloads</div>
                    </div>
                  </div>
                  <button type="button" className="ma-icon-dl-btn" aria-label={`Download ${item.name}`}>
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  </button>
                </div>
              ))}
            </div>
          </div>
        ))}
      </section>
    </div>
  );
}
