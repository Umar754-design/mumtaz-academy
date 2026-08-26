import '../mumtaz.css';

const posts = [
  {
    title: 'The Importance of Learning Arabic for Every Muslim',
    excerpt: 'Arabic is the language of the Quran, the Hadith, and the scholars. Understanding it deeply transforms your relationship with Islam and allows you to access knowledge at its source.',
    category: 'Arabic',
    date: 'June 18, 2025',
    readTime: '5 min read',
    bg: 'linear-gradient(135deg, #1b3a3a 0%, #0d5050 100%)',
    label: 'اَلۡعَرَبِيَّة',
  },
  {
    title: '10 Tips to Memorise the Quran Effectively',
    excerpt: 'Memorising the Quran (Hifz) is a lifelong achievement and a spiritual journey. These 10 practical tips will help you build a consistent and effective daily memorisation routine.',
    category: 'Quran',
    date: 'May 30, 2025',
    readTime: '7 min read',
    bg: 'linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%)',
    label: 'حِفْظ',
  },
  {
    title: 'Understanding the Pillars of Islam: A Beginner\'s Guide',
    excerpt: 'The five pillars of Islam form the foundation of a Muslim\'s faith and practice. This guide explains each pillar in simple terms and how they relate to everyday life.',
    category: 'Islamic Studies',
    date: 'May 10, 2025',
    readTime: '6 min read',
    bg: 'linear-gradient(135deg, #2d1a00 0%, #5c3500 100%)',
    label: 'إسلام',
  },
  {
    title: 'How Online Islamic Education Is Changing the Ummah',
    excerpt: 'The rise of online Islamic education has broken down geographical and financial barriers. Students from Nigeria to Norway can now access the same quality of Islamic teaching.',
    category: 'Education',
    date: 'April 22, 2025',
    readTime: '4 min read',
    bg: 'linear-gradient(135deg, #1a2d1a 0%, #2d5a2d 100%)',
    label: 'تَعْلِيم',
  },
  {
    title: 'Tajweed: Why Proper Quran Recitation Matters',
    excerpt: 'Tajweed is not just about beauty — it is about accuracy. Mispronunciation can change the meaning of Quranic words. Learn why mastering Tajweed is an obligation for every Muslim.',
    category: 'Quran',
    date: 'April 5, 2025',
    readTime: '5 min read',
    bg: 'linear-gradient(135deg, #1a1a2d 0%, #2d2d5a 100%)',
    label: 'تَجْوِيد',
  },
  {
    title: 'The Life of the Prophet ﷺ: Lessons for Modern Muslims',
    excerpt: 'The Seerah of the Prophet ﷺ is a timeless guide for all aspects of life. From leadership to family to business ethics — his example answers every question we face today.',
    category: 'Islamic Studies',
    date: 'March 20, 2025',
    readTime: '8 min read',
    bg: 'linear-gradient(135deg, #2d1a2d 0%, #5a2d5a 100%)',
    label: 'سِيرَة',
  },
];

const catColors: Record<string, string> = {
  'Quran': '#1a5c3e',
  'Arabic': '#1b3a6a',
  'Islamic Studies': '#5c3500',
  'Education': '#3a5c1a',
};

export default function Blog() {
  return (
    <div>
      <section className="ma-page-banner">
        <h1>Blog</h1>
        <p>Insights, guidance, and knowledge from our scholars</p>
      </section>

      <section style={{ padding: '60px 24px', background: '#f7f7f4' }}>
        <div style={{ maxWidth: '1100px', margin: '0 auto' }}>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(320px, 1fr))', gap: '28px' }}>
            {posts.map(p => (
              <article key={p.title} style={{ background: '#fff', borderRadius: '14px', overflow: 'hidden', boxShadow: '0 2px 12px rgba(0,0,0,0.07)', transition: 'transform 0.2s, box-shadow 0.2s' }}
                onMouseEnter={e => { (e.currentTarget as HTMLElement).style.transform = 'translateY(-4px)'; (e.currentTarget as HTMLElement).style.boxShadow = '0 8px 28px rgba(0,0,0,0.12)'; }}
                onMouseLeave={e => { (e.currentTarget as HTMLElement).style.transform = ''; (e.currentTarget as HTMLElement).style.boxShadow = ''; }}
              >
                {/* Image area */}
                <div style={{ height: '140px', background: p.bg, display: 'flex', alignItems: 'center', justifyContent: 'center', position: 'relative' }}>
                  <span style={{ fontFamily: 'serif', fontSize: '32px', color: 'rgba(255,255,255,0.2)' }}>{p.label}</span>
                  <span style={{
                    position: 'absolute', top: '12px', left: '12px',
                    background: catColors[p.category] || '#333',
                    color: '#fff', fontSize: '11px', fontWeight: 600,
                    padding: '3px 10px', borderRadius: '20px'
                  }}>{p.category}</span>
                </div>
                {/* Content */}
                <div style={{ padding: '20px' }}>
                  <div style={{ display: 'flex', gap: '12px', marginBottom: '10px', fontSize: '12px', color: '#999' }}>
                    <span>{p.date}</span>
                    <span>·</span>
                    <span>{p.readTime}</span>
                  </div>
                  <h3 style={{ fontSize: '16px', fontWeight: 700, color: '#111', lineHeight: '1.4', marginBottom: '10px' }}>{p.title}</h3>
                  <p style={{ fontSize: '13.5px', color: '#555', lineHeight: '1.65', marginBottom: '16px' }}>{p.excerpt}</p>
                  <a href="#" style={{ color: '#0b2b2b', fontWeight: 600, fontSize: '13px', textDecoration: 'none', borderBottom: '1.5px solid #c9a227' }}>Read More →</a>
                </div>
              </article>
            ))}
          </div>

          {/* Load more */}
          <div style={{ textAlign: 'center', marginTop: '40px' }}>
            <a href="#" className="ma-btn-dark">Load More Articles</a>
          </div>
        </div>
      </section>
    </div>
  );
}
