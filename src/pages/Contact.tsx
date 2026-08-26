import '../mumtaz.css';
import { useState } from 'react';

export default function Contact() {
  const [form, setForm] = useState({ name: '', email: '', subject: '', message: '' });
  const [sent, setSent] = useState(false);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setSent(true);
    setForm({ name: '', email: '', subject: '', message: '' });
  }

  return (
    <div>
      <section className="ma-page-banner">
        <h1>Contact Us</h1>
        <p>We are here to help — reach out to our team anytime</p>
      </section>

      <section style={{ padding: '70px 24px' }}>
        <div style={{ maxWidth: '1000px', margin: '0 auto', display: 'grid', gridTemplateColumns: '1fr 1.4fr', gap: '60px', alignItems: 'start' }}>

          {/* Info */}
          <div>
            <h2 style={{ fontSize: '26px', fontWeight: 800, color: '#0b2b2b', marginBottom: '16px' }}>Get In Touch</h2>
            <p style={{ color: '#555', lineHeight: '1.8', fontSize: '14.5px', marginBottom: '32px' }}>
              Have questions about our courses, live classes, or certificates? We'd love to hear from you. Our team typically responds within 24 hours.
            </p>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
              {[
                {
                  icon: (
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  ),
                  label: 'Address',
                  value: 'Baliapur, Dhanbad, Jharkhand, 828201, India',
                },
                {
                  icon: (
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  ),
                  label: 'Email',
                  value: 'khanumar865446@gmail.com',
                },
                {
                  icon: (
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.25h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 7.09 7.09l.91-1.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  ),
                  label: 'Phone',
                  value: '+91 12345 67890',
                },
                {
                  icon: (
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  ),
                  label: 'Office Hours',
                  value: 'Monday – Saturday, 9:00 AM – 6:00 PM IST',
                },
              ].map(item => (
                <div key={item.label} style={{ display: 'flex', alignItems: 'flex-start', gap: '14px' }}>
                  <div style={{ width: '42px', height: '42px', background: 'rgba(11,43,43,0.06)', borderRadius: '10px', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                    {item.icon}
                  </div>
                  <div>
                    <div style={{ fontSize: '11.5px', fontWeight: 700, color: '#888', textTransform: 'uppercase', letterSpacing: '0.5px', marginBottom: '2px' }}>{item.label}</div>
                    <div style={{ fontSize: '14px', color: '#333', lineHeight: '1.5' }}>{item.value}</div>
                  </div>
                </div>
              ))}
            </div>

            {/* Social */}
            <div style={{ marginTop: '32px' }}>
              <div style={{ fontSize: '13px', fontWeight: 700, color: '#0b2b2b', marginBottom: '12px', textTransform: 'uppercase', letterSpacing: '0.5px' }}>Follow Us</div>
              <div style={{ display: 'flex', gap: '10px' }}>
                {[
                  { label: 'Facebook', icon: 'f' },
                  { label: 'YouTube', icon: '▶' },
                  { label: 'Instagram', icon: '📷' },
                  { label: 'Telegram', icon: '✈' },
                ].map(s => (
                  <a key={s.label} href="#" aria-label={s.label} className="ma-social-icon" style={{ background: '#0b2b2b', color: '#fff' }}>{s.icon}</a>
                ))}
              </div>
            </div>
          </div>

          {/* Form */}
          <div style={{ background: '#f7f7f4', borderRadius: '16px', padding: '36px' }}>
            {sent ? (
              <div style={{ textAlign: 'center', padding: '40px 0' }}>
                <div style={{ fontSize: '48px', marginBottom: '16px' }}>✅</div>
                <h3 style={{ fontSize: '20px', fontWeight: 700, color: '#0b2b2b', marginBottom: '8px' }}>Message Sent!</h3>
                <p style={{ color: '#555', fontSize: '14px', lineHeight: '1.7' }}>Jazakallahu Khayran! We've received your message and will get back to you within 24 hours.</p>
                <button onClick={() => setSent(false)} className="ma-btn-dark" style={{ marginTop: '24px', cursor: 'pointer', border: 'none' }}>Send Another</button>
              </div>
            ) : (
              <>
                <h3 style={{ fontSize: '20px', fontWeight: 700, color: '#0b2b2b', marginBottom: '20px' }}>Send a Message</h3>
                <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '14px' }}>
                    <div className="ma-form-group">
                      <label>Your Name</label>
                      <input type="text" placeholder="e.g. Ahmad Ali" required value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} />
                    </div>
                    <div className="ma-form-group">
                      <label>Email Address</label>
                      <input type="email" placeholder="you@email.com" required value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} />
                    </div>
                  </div>
                  <div className="ma-form-group">
                    <label>Subject</label>
                    <select value={form.subject} onChange={e => setForm({ ...form, subject: e.target.value })} required>
                      <option value="">Select a topic...</option>
                      <option>Course Enquiry</option>
                      <option>Live Classes</option>
                      <option>Certificates</option>
                      <option>Technical Support</option>
                      <option>Teaching Opportunity</option>
                      <option>Other</option>
                    </select>
                  </div>
                  <div className="ma-form-group">
                    <label>Message</label>
                    <textarea rows={5} placeholder="Write your message here..." required value={form.message} onChange={e => setForm({ ...form, message: e.target.value })} />
                  </div>
                  <button type="submit" className="ma-btn-dark" style={{ cursor: 'pointer', border: 'none', padding: '13px', fontSize: '14px' }}>
                    Send Message →
                  </button>
                </form>
              </>
            )}
          </div>
        </div>
      </section>
    </div>
  );
}
