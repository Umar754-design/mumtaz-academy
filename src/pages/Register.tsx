import { useState } from 'react';
import { Link } from 'wouter';
import '../mumtaz.css';

const STEPS = ['Account', 'Profile', 'Done'];

export default function Register() {
  const [step, setStep] = useState(0);
  const [form, setForm] = useState({
    email: '', password: '', confirm: '',
    name: '', phone: '', level: '', interest: '',
    agree: false,
  });
  const [errors, setErrors] = useState<Record<string, string>>({});

  function validate() {
    const e: Record<string, string> = {};
    if (step === 0) {
      if (!form.email) e.email = 'Email is required.';
      else if (!/\S+@\S+\.\S+/.test(form.email)) e.email = 'Enter a valid email.';
      if (!form.password || form.password.length < 8) e.password = 'Password must be at least 8 characters.';
      if (form.password !== form.confirm) e.confirm = 'Passwords do not match.';
    }
    if (step === 1) {
      if (!form.name.trim()) e.name = 'Full name is required.';
      if (!form.level) e.level = 'Please select your level.';
      if (!form.agree) e.agree = 'You must accept the terms.';
    }
    return e;
  }

  function next(e: React.FormEvent) {
    e.preventDefault();
    const errs = validate();
    if (Object.keys(errs).length) { setErrors(errs); return; }
    setErrors({});
    setStep(s => s + 1);
  }

  const field = (key: keyof typeof form, val: string) => setForm(f => ({ ...f, [key]: val }));

  return (
    <div className="ma-auth-page">
      {/* Left panel */}
      <div className="ma-auth-left">
        <div className="ma-auth-left-inner">
          <Link href="/" className="ma-logo ma-auth-logo" aria-label="Mumtaz Academy Home">
            <div className="ma-logo-icon">
              <svg aria-hidden="true" width="44" height="44" viewBox="0 0 36 36" fill="none">
                <path d="M18 2 L34 12 L34 30 L18 34 L2 30 L2 12 Z" fill="#c9a227" opacity="0.2"/>
                <path d="M18 4 L32 13 L32 29 L18 32 L4 29 L4 13 Z" stroke="#c9a227" strokeWidth="1.5" fill="none"/>
                <rect x="14" y="16" width="8" height="12" rx="4" fill="#c9a227"/>
                <path d="M10 16 Q18 8 26 16" fill="none" stroke="#c9a227" strokeWidth="1.5"/>
                <path d="M6 18 Q18 6 30 18" fill="none" stroke="#c9a227" strokeWidth="1" opacity="0.6"/>
                <circle cx="18" cy="4" r="1.5" fill="#c9a227"/>
              </svg>
            </div>
            <div className="ma-logo-text">
              <span className="ma-logo-title" style={{ fontSize: '18px' }}>MUMTAZ</span>
              <span className="ma-logo-sub" style={{ fontSize: '11px' }}>ACADEMY</span>
            </div>
          </Link>

          {/* Step tracker */}
          <div className="ma-reg-steps">
            {STEPS.map((s, i) => (
              <div key={s} className={`ma-reg-step ${i <= step ? 'active' : ''} ${i < step ? 'done' : ''}`}>
                <div className="ma-reg-step-circle">
                  {i < step ? (
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><polyline points="20 6 9 17 4 12"/></svg>
                  ) : (i + 1)}
                </div>
                <span>{s}</span>
                {i < STEPS.length - 1 && <div className="ma-reg-step-line" />}
              </div>
            ))}
          </div>

          <div className="ma-auth-quote-block">
            <p className="ma-arabic" style={{ fontSize: '28px', marginBottom: '10px' }}>
              طَلَبُ الْعِلْمِ فَرِيضَةٌ
            </p>
            <p style={{ color: 'rgba(255,255,255,0.65)', fontSize: '13px', fontStyle: 'italic', marginBottom: '4px' }}>
              "Seeking knowledge is an obligation."
            </p>
            <p style={{ color: 'rgba(255,255,255,0.35)', fontSize: '12px' }}>(Ibn Majah)</p>
          </div>

          <div className="ma-auth-features">
            {[
              { icon: '✅', text: '100% Free Forever' },
              { icon: '🌍', text: 'Learn from Anywhere' },
              { icon: '📜', text: 'Unique Certificates' },
              { icon: '⏰', text: 'Learn at Your Pace' },
            ].map(f => (
              <div key={f.text} className="ma-auth-feature-item">
                <span>{f.icon}</span>
                <span>{f.text}</span>
              </div>
            ))}
          </div>
          <div className="ma-auth-arch" aria-hidden="true" />
        </div>
      </div>

      {/* Right panel */}
      <div className="ma-auth-right">
        <div className="ma-auth-form-wrap">
          {/* Mobile logo */}
          <Link href="/" className="ma-logo ma-auth-logo-mobile" aria-label="Mumtaz Academy Home">
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

          {step < 2 ? (
            <>
              {/* Step progress bar (mobile) */}
              <div className="ma-auth-progress">
                <div className="ma-auth-progress-bar" style={{ width: `${(step / (STEPS.length - 1)) * 100}%` }} />
              </div>
              <p className="ma-auth-step-label">Step {step + 1} of {STEPS.length - 1}</p>

              <h1 className="ma-auth-heading">
                {step === 0 ? 'Create Free Account' : 'Your Profile'}
              </h1>
              <p className="ma-auth-sub">
                {step === 0
                  ? 'Join 5,000+ students learning Islamic sciences for free.'
                  : 'Help us personalise your learning experience.'}
              </p>

              <form onSubmit={next} className="ma-auth-form" noValidate>
                {step === 0 && (
                  <>
                    <div className="ma-form-group">
                      <label htmlFor="reg-email">Email Address</label>
                      <div className="ma-input-icon-wrap">
                        <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input id="reg-email" type="email" placeholder="you@email.com" value={form.email} onChange={e => field('email', e.target.value)} />
                      </div>
                      {errors.email && <span className="ma-field-error">{errors.email}</span>}
                    </div>
                    <div className="ma-form-group">
                      <label htmlFor="reg-pass">Password</label>
                      <div className="ma-input-icon-wrap">
                        <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="reg-pass" type="password" placeholder="Min. 8 characters" value={form.password} onChange={e => field('password', e.target.value)} />
                      </div>
                      {errors.password && <span className="ma-field-error">{errors.password}</span>}
                    </div>
                    <div className="ma-form-group">
                      <label htmlFor="reg-confirm">Confirm Password</label>
                      <div className="ma-input-icon-wrap">
                        <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="reg-confirm" type="password" placeholder="Repeat password" value={form.confirm} onChange={e => field('confirm', e.target.value)} />
                      </div>
                      {errors.confirm && <span className="ma-field-error">{errors.confirm}</span>}
                    </div>
                  </>
                )}

                {step === 1 && (
                  <>
                    <div className="ma-form-group">
                      <label htmlFor="reg-name">Full Name</label>
                      <div className="ma-input-icon-wrap">
                        <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input id="reg-name" type="text" placeholder="e.g. Ahmad Ali" value={form.name} onChange={e => field('name', e.target.value)} />
                      </div>
                      {errors.name && <span className="ma-field-error">{errors.name}</span>}
                    </div>
                    <div className="ma-form-group">
                      <label htmlFor="reg-phone">Phone (optional)</label>
                      <div className="ma-input-icon-wrap">
                        <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.25h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 7.09 7.09l1.35-1.35a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <input id="reg-phone" type="tel" placeholder="+91 00000 00000" value={form.phone} onChange={e => field('phone', e.target.value)} />
                      </div>
                    </div>
                    <div className="ma-form-group">
                      <label htmlFor="reg-level">Your Current Level</label>
                      <select id="reg-level" value={form.level} onChange={e => field('level', e.target.value)}>
                        <option value="">Select your level...</option>
                        <option>Complete Beginner</option>
                        <option>Can Read Arabic (Nazra)</option>
                        <option>Intermediate</option>
                        <option>Advanced</option>
                      </select>
                      {errors.level && <span className="ma-field-error">{errors.level}</span>}
                    </div>
                    <div className="ma-form-group">
                      <label htmlFor="reg-interest">Primary Interest</label>
                      <select id="reg-interest" value={form.interest} onChange={e => field('interest', e.target.value)}>
                        <option value="">What do you want to learn?</option>
                        <option>Quran Recitation & Tajweed</option>
                        <option>Quran Memorisation (Hifz)</option>
                        <option>Arabic Language</option>
                        <option>Islamic Studies</option>
                        <option>Hadith Sciences</option>
                        <option>Everything!</option>
                      </select>
                    </div>
                    <div style={{ marginBottom: '4px' }}>
                      <label className="ma-checkbox-label">
                        <input type="checkbox" checked={form.agree} onChange={e => setForm(f => ({ ...f, agree: e.target.checked }))} />
                        <span className="ma-checkmark" />
                        I agree to the <a href="#" className="ma-auth-link">Terms of Service</a> and <a href="#" className="ma-auth-link">Privacy Policy</a>
                      </label>
                      {errors.agree && <span className="ma-field-error" style={{ display: 'block', marginTop: '4px' }}>{errors.agree}</span>}
                    </div>
                  </>
                )}

                <button type="submit" className="ma-auth-submit">
                  {step === 0 ? 'Continue →' : 'Create My Account'}
                </button>
              </form>

              {step === 0 && (
                <>
                  <div className="ma-auth-divider"><span>or sign up with</span></div>
                  <div className="ma-auth-social-btns">
                    <button className="ma-social-login-btn" type="button">
                      <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                      Google
                    </button>
                    <button className="ma-social-login-btn" type="button">
                      <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                      Facebook
                    </button>
                  </div>
                </>
              )}

              <p className="ma-auth-switch">
                Already have an account?{' '}
                <Link href="/login" className="ma-auth-link">Sign in →</Link>
              </p>
            </>
          ) : (
            /* Success screen */
            <div className="ma-auth-success">
              <div className="ma-auth-success-icon">
                <svg aria-hidden="true" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c9a227" strokeWidth="2"><circle cx="12" cy="12" r="10"/><polyline points="16 8 10 14 7 11"/></svg>
              </div>
              <h1 className="ma-auth-heading" style={{ textAlign: 'center' }}>Account Created!</h1>
              <p style={{ color: '#555', textAlign: 'center', lineHeight: '1.7', marginBottom: '28px' }}>
                Jazakallahu Khayran, <strong>{form.name || 'Student'}</strong>! Your free account is ready.<br />
                A confirmation email has been sent to <strong>{form.email}</strong>.
              </p>
              <div className="ma-auth-success-perks">
                {[
                  { icon: '📖', label: 'Access all 50+ courses' },
                  { icon: '📡', label: 'Join 100+ live classes' },
                  { icon: '📜', label: 'Earn verified certificates' },
                ].map(p => (
                  <div key={p.label} className="ma-auth-success-perk">
                    <span>{p.icon}</span><span>{p.label}</span>
                  </div>
                ))}
              </div>
              <Link href="/courses" className="ma-auth-submit" style={{ display: 'flex', justifyContent: 'center', textDecoration: 'none', marginTop: '20px' }}>
                Start Learning →
              </Link>
              <p className="ma-auth-switch" style={{ marginTop: '16px' }}>
                <Link href="/login" className="ma-auth-link">Sign in to your account</Link>
              </p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
