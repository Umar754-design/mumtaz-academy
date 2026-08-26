import '../mumtaz.css';
import { useState } from 'react';

const faqs = [
  {
    category: 'Getting Started',
    items: [
      {
        q: 'How do I enrol in a course?',
        a: 'Simply click "Enrol Now" on any course page. If you have an account, you will be enrolled instantly. If not, you will be prompted to register — it takes under 2 minutes and is completely free.',
      },
      {
        q: 'Is Mumtaz Academy really free?',
        a: 'Yes, 100%. Every course, every live class, every certificate, and every study material on Mumtaz Academy is completely free of charge. There are no hidden fees, subscriptions, or premium tiers.',
      },
      {
        q: 'Do I need to download any software?',
        a: 'No downloads required. All classes run directly in your browser using Zoom or Google Meet. A stable internet connection is all you need.',
      },
    ],
  },
  {
    category: 'Courses & Classes',
    items: [
      {
        q: 'Can I access course recordings after a live class?',
        a: 'Yes. All live classes are recorded and uploaded to the student portal within 24 hours. You can watch them at any time at your own pace.',
      },
      {
        q: 'What if I miss a live class?',
        a: 'No problem. Recordings of all sessions are available in your dashboard. You can catch up anytime and continue from where others left off.',
      },
      {
        q: 'Can beginners with no prior knowledge join?',
        a: 'Absolutely. We have courses specifically designed for complete beginners — including Noorani Qaida for Quran reading and an Arabic Alphabet course. You do not need any prior knowledge to start.',
      },
    ],
  },
  {
    category: 'Certificates',
    items: [
      {
        q: 'How do I get my certificate?',
        a: 'Certificates are issued automatically upon completing a course and passing the final MCQ assessment with a score of 60% or higher. You can download your certificate as a PDF from your dashboard.',
      },
      {
        q: 'Are the certificates recognised?',
        a: 'Our certificates carry a unique verification ID and are signed by the course scholar. They are widely accepted as proof of Islamic learning completion. However, they are not government-accredited academic qualifications.',
      },
    ],
  },
  {
    category: 'Technical Issues',
    items: [
      {
        q: 'I cannot log in to my account. What should I do?',
        a: 'First, try the "Forgot password?" link on the login page. If you still cannot access your account, email us at khanumar865446@gmail.com with the email address associated with your account.',
      },
      {
        q: 'The live class link is not working.',
        a: 'Make sure you are clicking the link at the correct class time. Links are activated 5 minutes before the scheduled start. If the problem persists, contact us on Telegram or WhatsApp for immediate help.',
      },
    ],
  },
];

const contactOptions = [
  {
    icon: (
      <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    ),
    title: 'Email Support',
    desc: 'Detailed questions — response within 24 hours.',
    action: 'Send an Email',
    href: 'mailto:khanumar865446@gmail.com',
    tag: '24hr response',
  },
  {
    icon: (
      <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    ),
    title: 'Telegram Group',
    desc: 'Live chat with students and teachers.',
    action: 'Join Telegram',
    href: '#',
    tag: 'Most Popular',
  },
  {
    icon: (
      <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.25h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 7.09 7.09l1.35-1.35a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
    ),
    title: 'WhatsApp',
    desc: 'Quick queries — Mon to Sat, 9 AM – 6 PM IST.',
    action: 'WhatsApp Us',
    href: '#',
    tag: 'Fast',
  },
];

export default function StudentSupport() {
  const [open, setOpen] = useState<string | null>(null);

  function toggle(key: string) {
    setOpen(o => (o === key ? null : key));
  }

  return (
    <div>
      <section className="ma-page-banner">
        <h1>Student Support</h1>
        <p>We're here to help you every step of your learning journey</p>
      </section>

      {/* Contact options */}
      <section style={{ background: '#f7f7f4', padding: '50px 24px' }}>
        <div style={{ maxWidth: '1000px', margin: '0 auto' }}>
          <h2 style={{ textAlign: 'center', fontWeight: 800, fontSize: '24px', marginBottom: '8px' }}>How to Reach Us</h2>
          <p style={{ textAlign: 'center', color: '#666', fontSize: '14px', marginBottom: '36px' }}>Choose the channel that works best for you</p>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(260px, 1fr))', gap: '20px' }}>
            {contactOptions.map(opt => (
              <div key={opt.title} style={{ background: '#fff', borderRadius: '14px', padding: '28px', boxShadow: '0 2px 12px rgba(0,0,0,0.06)', position: 'relative', border: '1.5px solid #eee' }}>
                {opt.tag && (
                  <span style={{ position: 'absolute', top: '14px', right: '14px', background: '#c9a227', color: '#0b2b2b', fontSize: '10.5px', fontWeight: 700, padding: '2px 9px', borderRadius: '20px' }}>{opt.tag}</span>
                )}
                <div style={{ width: '52px', height: '52px', background: 'rgba(11,43,43,0.07)', borderRadius: '12px', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#0b2b2b', marginBottom: '16px' }}>{opt.icon}</div>
                <h3 style={{ fontSize: '16px', fontWeight: 700, marginBottom: '6px' }}>{opt.title}</h3>
                <p style={{ fontSize: '13.5px', color: '#666', lineHeight: '1.6', marginBottom: '16px' }}>{opt.desc}</p>
                <a href={opt.href} className="ma-enroll-btn" style={{ display: 'inline-block', fontSize: '13px' }}>{opt.action} →</a>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQs */}
      <section style={{ padding: '60px 24px', maxWidth: '800px', margin: '0 auto' }}>
        <h2 style={{ textAlign: 'center', fontWeight: 800, fontSize: '24px', marginBottom: '8px' }}>Frequently Asked Questions</h2>
        <p style={{ textAlign: 'center', color: '#666', fontSize: '14px', marginBottom: '40px' }}>Can't find your answer? Contact us directly.</p>

        {faqs.map(cat => (
          <div key={cat.category} style={{ marginBottom: '36px' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '14px' }}>
              <div style={{ width: '4px', height: '20px', background: '#c9a227', borderRadius: '2px' }} />
              <h3 style={{ fontSize: '15px', fontWeight: 700, color: '#0b2b2b', textTransform: 'uppercase', letterSpacing: '0.5px' }}>{cat.category}</h3>
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
              {cat.items.map((item, i) => {
                const key = `${cat.category}-${i}`;
                const panelId = `faq-panel-${cat.category.replace(/\s+/g, '-')}-${i}`;
                const triggerId = `faq-btn-${cat.category.replace(/\s+/g, '-')}-${i}`;
                const isOpen = open === key;
                return (
                  <div key={key} style={{ border: `1.5px solid ${isOpen ? '#0b2b2b' : '#e8e8e8'}`, borderRadius: '10px', overflow: 'hidden', transition: 'border-color 0.2s' }}>
                    <button
                      id={triggerId}
                      aria-expanded={isOpen}
                      aria-controls={panelId}
                      onClick={() => toggle(key)}
                      style={{ width: '100%', padding: '16px 18px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: isOpen ? '#0b2b2b' : '#fff', border: 'none', cursor: 'pointer', textAlign: 'left', gap: '12px', fontFamily: 'inherit' }}
                    >
                      <span style={{ fontSize: '14.5px', fontWeight: 600, color: isOpen ? '#fff' : '#111' }}>{item.q}</span>
                      <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke={isOpen ? '#c9a227' : '#999'} strokeWidth="2.5" style={{ flexShrink: 0, transform: isOpen ? 'rotate(180deg)' : '', transition: 'transform 0.2s' }}><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    {isOpen && (
                      <div id={panelId} role="region" aria-labelledby={triggerId} style={{ padding: '16px 18px', background: '#f7faf7', fontSize: '14px', color: '#444', lineHeight: '1.75' }}>
                        {item.a}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        ))}
      </section>

      {/* CTA */}
      <section style={{ background: '#0b2b2b', padding: '50px 24px', textAlign: 'center' }}>
        <h2 style={{ color: '#fff', fontSize: '22px', fontWeight: 800, marginBottom: '10px' }}>Still Have Questions?</h2>
        <p style={{ color: 'rgba(255,255,255,0.6)', fontSize: '14px', marginBottom: '24px' }}>Our support team is available Mon–Sat, 9 AM to 6 PM IST.</p>
        <a href="/contact" className="ma-btn-gold" style={{ borderRadius: '8px', padding: '12px 28px', fontSize: '14px', textDecoration: 'none' }}>Go to Contact Page</a>
      </section>
    </div>
  );
}
