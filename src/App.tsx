import { useState } from 'react';
import { Route, Switch, Link, useLocation } from 'wouter';
import './index.css';
import './mumtaz.css';

import Courses from './pages/Courses';
import LiveClasses from './pages/LiveClasses';
import AboutUs from './pages/AboutUs';
import Teachers from './pages/Teachers';
import Blog from './pages/Blog';
import Contact from './pages/Contact';
import Login from './pages/Login';
import Register from './pages/Register';
import StudyMaterials from './pages/StudyMaterials';
import McqTests from './pages/McqTests';
import StudentSupport from './pages/StudentSupport';
import PrivacyPolicy from './pages/PrivacyPolicy';

/* ─── NAV LINKS ─── */
const NAV_LINKS = [
  { label: 'Home',         href: '/' },
  { label: 'Courses',      href: '/courses' },
  { label: 'Live Classes', href: '/live-classes' },
  { label: 'About Us',     href: '/about' },
  { label: 'Teachers',     href: '/teachers' },
  { label: 'Blog',         href: '/blog' },
  { label: 'Contact',      href: '/contact' },
];

/* ─── ROOT APP ─── */
export default function App() {
  return (
    <>
      <Navbar />
      <main>
        <Switch>
          <Route path="/"            component={Home} />
          <Route path="/courses"     component={Courses} />
          <Route path="/live-classes" component={LiveClasses} />
          <Route path="/about"       component={AboutUs} />
          <Route path="/teachers"    component={Teachers} />
          <Route path="/blog"        component={Blog} />
          <Route path="/contact"     component={Contact} />
          <Route path="/login"            component={Login} />
          <Route path="/register"         component={Register} />
          <Route path="/study-materials"  component={StudyMaterials} />
          <Route path="/mcq-tests"        component={McqTests} />
          <Route path="/student-support"  component={StudentSupport} />
          <Route path="/privacy-policy"   component={PrivacyPolicy} />
          <Route>
            <div style={{ textAlign: 'center', padding: '100px 24px' }}>
              <h1 style={{ fontSize: '48px', color: '#0b2b2b' }}>404</h1>
              <p style={{ color: '#555', marginBottom: '20px' }}>Page not found.</p>
              <Link href="/" className="ma-btn-dark">Go Home</Link>
            </div>
          </Route>
        </Switch>
      </main>
      <Footer />
    </>
  );
}

/* ─── NAVBAR ─── */
function Navbar() {
  const [open, setOpen] = useState(false);
  const [location] = useLocation();

  return (
    <nav className="ma-nav">
      <div className="ma-nav-inner">
        <Link href="/" className="ma-logo" aria-label="Mumtaz Academy Home">
          <div className="ma-logo-icon">
            <svg aria-hidden="true" width="36" height="36" viewBox="0 0 36 36" fill="none">
              <path d="M18 2 L34 12 L34 30 L18 34 L2 30 L2 12 Z" fill="#c9a227" opacity="0.2"/>
              <path d="M18 4 L32 13 L32 29 L18 32 L4 29 L4 13 Z" stroke="#c9a227" strokeWidth="1.5" fill="none"/>
              <rect x="14" y="16" width="8" height="12" rx="4" fill="#c9a227"/>
              <path d="M10 16 Q18 8 26 16" fill="none" stroke="#c9a227" strokeWidth="1.5"/>
              <path d="M6 18 Q18 6 30 18" fill="none" stroke="#c9a227" strokeWidth="1" opacity="0.6"/>
              <circle cx="18" cy="4" r="1.5" fill="#c9a227"/>
            </svg>
          </div>
          <div className="ma-logo-text">
            <span className="ma-logo-title">MUMTAZ</span>
            <span className="ma-logo-sub">ACADEMY</span>
          </div>
        </Link>

        <ul className="ma-nav-links">
          {NAV_LINKS.map(l => (
            <li key={l.href}>
              <Link href={l.href} className={location === l.href ? 'active' : ''}>
                {l.label}
              </Link>
            </li>
          ))}
        </ul>

        <div className="ma-nav-actions">
          <Link href="/login"    className="ma-btn-outline">Login</Link>
          <Link href="/register" className="ma-btn-gold">Register</Link>
          <button
            className="ma-hamburger"
            aria-label={open ? 'Close menu' : 'Open menu'}
            aria-expanded={open}
            onClick={() => setOpen(o => !o)}
          >
            <span /><span /><span />
          </button>
        </div>
      </div>

      {open && (
        <div className="ma-mobile-menu" role="navigation" aria-label="Mobile navigation">
          <ul>
            {NAV_LINKS.map(l => (
              <li key={l.href}>
                <Link href={l.href} onClick={() => setOpen(false)} className={location === l.href ? 'active' : ''}>
                  {l.label}
                </Link>
              </li>
            ))}
          </ul>
          <div className="ma-mobile-actions">
            <Link href="/login"    className="ma-btn-outline" onClick={() => setOpen(false)}>Login</Link>
            <Link href="/register" className="ma-btn-gold" onClick={() => setOpen(false)}>Register</Link>
          </div>
        </div>
      )}
    </nav>
  );
}

/* ─── HOME PAGE ─── */
function Home() {
  return (
    <>
      <Hero />
      <QuoteBanner />
      <CoursesSection />
      <WhyChooseSection />
      <CTABanner />
    </>
  );
}

/* ─── HERO ─── */
function Hero() {
  return (
    <section className="ma-hero">
      <div className="ma-hero-overlay" />
      <div className="ma-hero-arch" />
      <div className="ma-hero-content">
        <div className="ma-hero-text">
          <h1 className="ma-hero-heading">
            Online Learning<br />
            with an <span className="ma-gold">Islamic Touch</span>
          </h1>
          <p className="ma-hero-sub">
            Learn Quran, Arabic, Islamic Studies and more from<br />
            the comfort of your home with expert scholars.
          </p>
          <div className="ma-hero-btns">
            <Link href="/courses" className="ma-btn-hero-gold">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
              Explore Courses
            </Link>
            <Link href="/live-classes" className="ma-btn-hero-outline">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" fill="rgba(255,255,255,0.15)" stroke="white" strokeWidth="1.5"/><polygon points="10,8 16,12 10,16" fill="white"/></svg>
              Watch Intro
            </Link>
          </div>
          <div className="ma-hero-badges">
            <div className="ma-badge">
              <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
              <div><strong>100% Free Courses</strong><span>For All</span></div>
            </div>
            <div className="ma-badge">
              <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
              <div><strong>Live Classes</strong><span>Interactive Sessions</span></div>
            </div>
            <div className="ma-badge">
              <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><circle cx="12" cy="8" r="6"/><path d="M8 14l-6 8M16 14l6 8"/></svg>
              <div><strong>Certificates</strong><span>Verified &amp; Trusted</span></div>
            </div>
          </div>
        </div>
        <div className="ma-hero-img-wrap">
          <div className="ma-hero-img-bg" />
        </div>
      </div>
    </section>
  );
}

/* ─── QUOTE BANNER ─── */
function QuoteBanner() {
  return (
    <section className="ma-quote">
      <div className="ma-quote-inner">
        <div className="ma-quote-left">
          <div className="ma-quote-book">
            <svg aria-hidden="true" width="48" height="48" viewBox="0 0 48 48" fill="none">
              <rect x="8" y="6" width="32" height="36" rx="2" fill="#c9a227" opacity="0.2" stroke="#c9a227" strokeWidth="1.5"/>
              <line x1="8" y1="24" x2="40" y2="24" stroke="#c9a227" strokeWidth="1" opacity="0.5"/>
              <path d="M16 14 Q24 10 32 14" stroke="#c9a227" strokeWidth="1.5" fill="none"/>
            </svg>
          </div>
          <div>
            <p className="ma-arabic">وَقُل رَّبِّ زِدۡنِي عِلۡمًا</p>
            <p className="ma-quote-trans">'Aur keh do: Mere Rabb! mujhe ilm mein izafa ata farma.'</p>
            <p className="ma-quote-ref">(Surah Taha : 114)</p>
          </div>
        </div>
        <div className="ma-quote-features">
          {[
            { icon: '👨‍🏫', label: 'Expert Scholars' },
            { icon: '📖', label: 'Easy to Learn' },
            { icon: '⏰', label: 'Anytime Anywhere' },
            { icon: '🎧', label: 'Student Support' },
          ].map(f => (
            <div key={f.label} className="ma-quote-feat">
              <div className="ma-feat-icon">{f.icon}</div>
              <span>{f.label}</span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

/* ─── COURSES PREVIEW (Home) ─── */
function CoursesSection() {
  const courses = [
    { title: 'Quran Learning', desc: 'Learn Quran with Tajweed, recitation and understanding.', icon: '📚', bg: 'linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%)', label: 'ٱلۡقُرۡءَانُ' },
    { title: 'Arabic Language', desc: 'Learn Arabic reading, writing, grammar and conversation.', icon: 'ع', bg: 'linear-gradient(135deg, #1b3a3a 0%, #0d5050 100%)', label: 'اَلۡعَرَبِيَّة' },
    { title: 'Islamic Studies', desc: 'Learn Aqaid, Fiqh, Seerah, Hadith and much more.', icon: '🕌', bg: 'linear-gradient(135deg, #2d1a00 0%, #5c3500 100%)', label: '' },
  ];
  return (
    <section className="ma-courses">
      <div className="ma-section-header">
        <h2>Our Courses</h2>
        <div className="ma-divider">
          <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="#c9a227"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>
        </div>
        <p>Quality Islamic Education for Everyone</p>
      </div>
      <div className="ma-courses-grid">
        {courses.map(c => (
          <div className="ma-course-card" key={c.title}>
            <div className="ma-course-img" style={{ background: c.bg }}>
              <div className="ma-course-img-text">{c.label}</div>
              <div className="ma-course-img-icon">{c.icon}</div>
            </div>
            <div className="ma-course-body">
              <h3>{c.title}</h3>
              <p>{c.desc}</p>
              <div className="ma-course-footer">
                <span className="ma-free-badge">Free Course</span>
                <Link href="/courses" className="ma-enroll-btn">Enroll Now →</Link>
              </div>
            </div>
          </div>
        ))}
      </div>
      <div className="ma-view-all">
        <Link href="/courses" className="ma-btn-dark">View All Courses →</Link>
      </div>
    </section>
  );
}

/* ─── WHY CHOOSE ─── */
function WhyChooseSection() {
  return (
    <section className="ma-why">
      <div className="ma-why-inner">
        <div className="ma-why-left">
          <div className="ma-lantern" aria-hidden="true">
            <svg width="80" height="160" viewBox="0 0 80 160" fill="none">
              <line x1="40" y1="0" x2="40" y2="20" stroke="#c9a227" strokeWidth="3"/>
              <ellipse cx="40" cy="22" rx="16" ry="6" fill="#c9a227"/>
              <rect x="20" y="22" width="40" height="80" rx="8" fill="#c9a227" opacity="0.15" stroke="#c9a227" strokeWidth="1.5"/>
              <line x1="30" y1="22" x2="30" y2="102" stroke="#c9a227" strokeWidth="1" opacity="0.5"/>
              <line x1="40" y1="22" x2="40" y2="102" stroke="#c9a227" strokeWidth="1" opacity="0.5"/>
              <line x1="50" y1="22" x2="50" y2="102" stroke="#c9a227" strokeWidth="1" opacity="0.5"/>
              <ellipse cx="40" cy="102" rx="20" ry="8" fill="#c9a227" opacity="0.4"/>
              <path d="M30 102 Q20 120 25 140 Q40 155 55 140 Q60 120 50 102 Z" fill="#c9a227" opacity="0.3" stroke="#c9a227" strokeWidth="1"/>
              <ellipse cx="40" cy="62" rx="8" ry="12" fill="#f5d76e" opacity="0.6"/>
            </svg>
          </div>
          <div className="ma-why-text">
            <h2>Why Choosee<br /><span className="ma-gold">Mumtaz Academy?</span></h2>
            <ul className="ma-why-list">
              {[
                'Qualified and experienced Islamic scholars',
                'Live interactive classes with students',
                'Study materials and recordings available',
                'MCQ tests and assessments',
                'Certificates with unique ID',
              ].map(item => (
                <li key={item}>
                  <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/></svg>
                  {item}
                </li>
              ))}
            </ul>
          </div>
        </div>
        <div className="ma-why-stats">
          {[
            { num: '5000+', label: 'Happy Students', icon: '👥' },
            { num: '50+',   label: 'Courses', icon: '📚' },
            { num: '100+',  label: 'Live Classes', icon: '🎬' },
            { num: '2000+', label: 'Certificates Issued', icon: '🏅' },
          ].map(s => (
            <div key={s.label} className="ma-stat-card">
              <div className="ma-stat-icon">{s.icon}</div>
              <div className="ma-stat-num">{s.num}</div>
              <div className="ma-stat-label">{s.label}</div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

/* ─── CTA BANNER ─── */
function CTABanner() {
  return (
    <section className="ma-cta">
      <div className="ma-cta-inner">
        <div className="ma-cta-mosque" aria-hidden="true">
          <svg width="90" height="90" viewBox="0 0 90 90" fill="none">
            <rect x="25" y="50" width="40" height="30" rx="4" fill="#0b2b2b"/>
            <path d="M25 50 Q45 28 65 50" fill="#1a4a4a"/>
            <rect x="35" y="55" width="20" height="25" rx="2" fill="#0d3b3b"/>
            <path d="M10 60 Q20 40 30 50" stroke="#0b2b2b" strokeWidth="3" fill="none"/>
            <path d="M80 60 Q70 40 60 50" stroke="#0b2b2b" strokeWidth="3" fill="none"/>
            <line x1="45" y1="15" x2="45" y2="28" stroke="#c9a227" strokeWidth="2"/>
            <circle cx="45" cy="13" r="3" fill="#c9a227"/>
          </svg>
        </div>
        <div className="ma-cta-text">
          <h2>Start Your Journey of Islamic Education</h2>
          <p>Join thousands of students and learn deen with ease.</p>
        </div>
        <Link href="/courses" className="ma-btn-cta">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Join Now – It's Free
        </Link>
      </div>
    </section>
  );
}

/* ─── FOOTER ─── */
function Footer() {
  return (
    <footer className="ma-footer">
      <div className="ma-footer-top">
        <div className="ma-footer-brand">
          <Link href="/" className="ma-logo" style={{ marginBottom: '14px', display: 'inline-flex' }} aria-label="Mumtaz Academy Home">
            <div className="ma-logo-icon">
              <svg aria-hidden="true" width="32" height="32" viewBox="0 0 36 36" fill="none">
                <path d="M18 4 L32 13 L32 29 L18 32 L4 29 L4 13 Z" stroke="#c9a227" strokeWidth="1.5" fill="none"/>
                <rect x="14" y="16" width="8" height="12" rx="4" fill="#c9a227"/>
                <path d="M10 16 Q18 8 26 16" fill="none" stroke="#c9a227" strokeWidth="1.5"/>
              </svg>
            </div>
            <div className="ma-logo-text">
              <span className="ma-logo-title">MUMTAZ</span>
              <span className="ma-logo-sub">ACADEMY</span>
            </div>
          </Link>
          <p className="ma-footer-desc">Mumtaz Academy is an Islamic online learning platform providing free education to everyone, everywhere.</p>
        </div>

        <div className="ma-footer-col">
          <h4>Quick Links</h4>
          <ul>
            {[
              { label: 'Home',        href: '/' },
              { label: 'Courses',     href: '/courses' },
              { label: 'Live Classes',href: '/live-classes' },
              { label: 'About Us',    href: '/about' },
              { label: 'Contact Us',  href: '/contact' },
            ].map(l => <li key={l.href}><Link href={l.href}>{l.label}</Link></li>)}
          </ul>
        </div>

        <div className="ma-footer-col">
          <h4>Resources</h4>
          <ul>
            {[
              { label: 'Blog',            href: '/blog' },
              { label: 'Study Materials', href: '/study-materials' },
              { label: 'MCQ Tests',       href: '/mcq-tests' },
              { label: 'Student Support', href: '/student-support' },
              { label: 'Privacy Policy',  href: '/privacy-policy' },
            ].map(l => (
              <li key={l.href}><Link href={l.href}>{l.label}</Link></li>
            ))}
          </ul>
        </div>

        <div className="ma-footer-col">
          <h4>Contact Us</h4>
          <ul className="ma-contact-list">
            <li>
              <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Baliapur, Dhanbad, Jharkhand, 828201
            </li>
            <li>
              <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              khanumar865446@gmail.com
            </li>
            <li>
              <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.25h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 7.09 7.09l.91-1.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              +91 12345 67890
            </li>
          </ul>
          <div className="ma-social">
            <h4>Follow Us</h4>
            <div className="ma-social-icons">
              {[
                { icon: 'f', label: 'Facebook' },
                { icon: '▶', label: 'YouTube' },
                { icon: '📷', label: 'Instagram' },
                { icon: '✈', label: 'Telegram' },
              ].map(s => (
                <a key={s.label} href="#" className="ma-social-icon" aria-label={s.label}>{s.icon}</a>
              ))}
            </div>
          </div>
        </div>
      </div>
      <div className="ma-footer-bottom">
        <p>© 2025 Mumtaz Academy. All Rights Reserved.</p>
      </div>
    </footer>
  );
}
