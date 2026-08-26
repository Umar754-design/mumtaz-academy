import '../mumtaz.css';
import { Link } from 'wouter';

const sections = [
  {
    id: 'information',
    title: '1. Information We Collect',
    content: `When you register for a Mumtaz Academy account, we collect:

• **Name and email address** — to create and manage your account.
• **Phone number** (optional) — if you choose to provide it for WhatsApp support.
• **Learning preferences** — your current level and subjects of interest, used to personalise your course recommendations.
• **Usage data** — pages visited, courses enrolled in, quiz scores, and class attendance, used to improve our platform and your experience.

We do not collect payment information because all our services are free.`,
  },
  {
    id: 'use',
    title: '2. How We Use Your Information',
    content: `We use your information solely to:

• **Provide and improve our services** — including courses, live classes, certificates, and study materials.
• **Send you relevant updates** — such as new course announcements, class reminders, and certificates. You may unsubscribe at any time.
• **Personalise your experience** — by recommending courses suited to your level and interests.
• **Respond to your support requests** — when you contact us via email, Telegram, or WhatsApp.
• **Maintain platform security** — to detect and prevent fraudulent or abusive activity.

We will never sell, rent, or trade your personal data to third parties for marketing purposes.`,
  },
  {
    id: 'cookies',
    title: '3. Cookies & Tracking',
    content: `We use minimal, essential cookies to:

• Keep you logged in to your account across sessions (authentication cookies).
• Remember your preferences such as language and display settings.
• Collect anonymous analytics data (page views, course completion rates) through privacy-respecting tools.

You can disable cookies in your browser settings, but this may prevent certain features from working correctly. We do not use tracking cookies for advertising purposes.`,
  },
  {
    id: 'sharing',
    title: '4. Data Sharing',
    content: `We do not share your personal data with third parties, except in the following limited circumstances:

• **Service providers** — trusted vendors who help us operate the platform (e.g. email delivery, video hosting). They are bound by strict data processing agreements and may only use your data to provide services to us.
• **Legal obligations** — if required by law, court order, or government authority, we may disclose information as required.
• **Safety** — if we believe disclosure is necessary to protect the rights, property, or safety of Mumtaz Academy, our users, or the public.

We will never sell your personal data.`,
  },
  {
    id: 'retention',
    title: '5. Data Retention',
    content: `We retain your personal data for as long as your account is active or as needed to provide services. If you delete your account, we will permanently delete your personal data within 30 days, except where we are required to retain it for legal obligations.

Course completion records and certificate data may be retained for a longer period to allow you to verify your credentials in the future.`,
  },
  {
    id: 'rights',
    title: '6. Your Rights',
    content: `You have the following rights regarding your personal data:

• **Access** — request a copy of the personal data we hold about you.
• **Correction** — ask us to update or correct inaccurate data.
• **Deletion** — request that we delete your account and personal data.
• **Portability** — receive your data in a commonly used, machine-readable format.
• **Opt-out** — unsubscribe from marketing emails at any time using the link in any email we send.

To exercise any of these rights, contact us at khanumar865446@gmail.com.`,
  },
  {
    id: 'security',
    title: '7. Security',
    content: `We implement industry-standard security measures to protect your data, including:

• HTTPS encryption for all data transmitted between your browser and our servers.
• Secure password hashing — your password is never stored in plain text.
• Regular security audits and vulnerability assessments.

However, no method of transmission over the internet or electronic storage is 100% secure. We encourage you to use a strong, unique password for your account.`,
  },
  {
    id: 'children',
    title: '8. Children\'s Privacy',
    content: `Mumtaz Academy welcomes students of all ages, including children. For students under the age of 13, we require parental or guardian consent before creating an account. We do not knowingly collect personal data from children under 13 without verified parental consent.

If you believe we have collected data from a child without appropriate consent, please contact us immediately at khanumar865446@gmail.com and we will delete that information promptly.`,
  },
  {
    id: 'changes',
    title: '9. Changes to This Policy',
    content: `We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. When we make material changes, we will notify you by email or by posting a prominent notice on our website at least 14 days before the changes take effect.

Your continued use of Mumtaz Academy after the effective date of any changes constitutes your acceptance of the updated policy.`,
  },
  {
    id: 'contact',
    title: '10. Contact Us',
    content: `If you have any questions, concerns, or requests regarding this Privacy Policy or your personal data, please contact us:

• **Email:** khanumar865446@gmail.com
• **Address:** Baliapur, Dhanbad, Jharkhand, 828201, India
• **Office Hours:** Monday – Saturday, 9:00 AM – 6:00 PM IST

We aim to respond to all privacy-related inquiries within 5 business days.`,
  },
];

function renderContent(text: string) {
  return text.split('\n').map((line, i) => {
    if (!line.trim()) return <br key={i} />;
    // Bold text wrapped in **
    const parts = line.split(/\*\*(.*?)\*\*/g);
    return (
      <p key={i} style={{ margin: '0 0 6px', lineHeight: '1.75', fontSize: '14.5px', color: '#444' }}>
        {parts.map((part, j) => j % 2 === 1 ? <strong key={j} style={{ color: '#222' }}>{part}</strong> : part)}
      </p>
    );
  });
}

export default function PrivacyPolicy() {
  return (
    <div>
      <section className="ma-page-banner">
        <h1>Privacy Policy</h1>
        <p>Last updated: June 1, 2025</p>
      </section>

      <section className="ma-privacy-layout">

        {/* Sticky table of contents */}
        <nav className="ma-privacy-toc" style={{ position: 'sticky', top: '90px' }} aria-label="Privacy policy sections">
          <div style={{ fontSize: '11.5px', fontWeight: 700, color: '#999', textTransform: 'uppercase', letterSpacing: '0.5px', marginBottom: '12px' }}>Contents</div>
          <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: '6px' }}>
            {sections.map(s => (
              <li key={s.id}>
                <a href={`#${s.id}`} style={{ fontSize: '13px', color: '#555', textDecoration: 'none', padding: '4px 0', display: 'block', borderLeft: '2px solid transparent', paddingLeft: '10px', transition: 'color 0.2s, border-color 0.2s' }}
                  onMouseEnter={e => { (e.currentTarget as HTMLElement).style.color = '#0b2b2b'; (e.currentTarget as HTMLElement).style.borderLeftColor = '#c9a227'; }}
                  onMouseLeave={e => { (e.currentTarget as HTMLElement).style.color = '#555'; (e.currentTarget as HTMLElement).style.borderLeftColor = 'transparent'; }}
                >
                  {s.title}
                </a>
              </li>
            ))}
          </ul>
        </nav>

        {/* Content */}
        <div>
          {/* Intro */}
          <div style={{ background: '#f7faf7', border: '1px solid #c9e8c9', borderRadius: '10px', padding: '18px 20px', marginBottom: '36px' }}>
            <p style={{ fontSize: '14px', color: '#2d5a2d', lineHeight: '1.7', margin: 0 }}>
              At Mumtaz Academy, your privacy is important to us. This policy explains what data we collect, how we use it, and your rights over it. We are committed to handling your information with care, transparency, and respect.
            </p>
          </div>

          {sections.map(s => (
            <section key={s.id} id={s.id} style={{ marginBottom: '40px', scrollMarginTop: '90px' }}>
              <h2 style={{ fontSize: '18px', fontWeight: 800, color: '#0b2b2b', marginBottom: '14px', paddingBottom: '8px', borderBottom: '1.5px solid #f0f0f0' }}>
                {s.title}
              </h2>
              <div>{renderContent(s.content)}</div>
            </section>
          ))}

          {/* Bottom CTA */}
          <div style={{ background: '#0b2b2b', borderRadius: '12px', padding: '28px', textAlign: 'center', marginTop: '16px' }}>
            <p style={{ color: 'rgba(255,255,255,0.7)', fontSize: '14px', marginBottom: '16px' }}>
              Have a question about your data or this policy?
            </p>
            <Link href="/contact" className="ma-btn-gold" style={{ textDecoration: 'none', padding: '10px 24px', borderRadius: '8px', fontSize: '14px' }}>
              Contact Us
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
