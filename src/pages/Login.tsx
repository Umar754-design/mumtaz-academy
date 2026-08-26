import { useState } from 'react';
import { Link } from 'wouter';
import '../mumtaz.css';

export default function Login() {
  const [form, setForm] = useState({ email: '', password: '', remember: false });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError('');
    setLoading(true);
    // Simulate auth delay
    setTimeout(() => {
      setLoading(false);
      setError('Invalid email or password. Please try again.');
    }, 1200);
  }

  return (
    <div className="ma-auth-page">
      {/* Left panel — decorative */}
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

          <div className="ma-auth-quote-block">
            <p className="ma-arabic" style={{ fontSize: '32px', marginBottom: '10px' }}>
              وَقُل رَّبِّ زِدۡنِي عِلۡمًا
            </p>
            <p style={{ color: 'rgba(255,255,255,0.65)', fontSize: '13px', fontStyle: 'italic', marginBottom: '4px' }}>
              "My Lord, increase me in knowledge."
            </p>
            <p style={{ color: 'rgba(255,255,255,0.35)', fontSize: '12px' }}>(Surah Taha : 114)</p>
          </div>

          <div className="ma-auth-features">
            {[
              { icon: '📖', text: '50+ Free Courses' },
              { icon: '🎓', text: 'Verified Certificates' },
              { icon: '📡', text: '100+ Live Classes' },
              { icon: '👨‍🏫', text: 'Expert Scholars' },
            ].map(f => (
              <div key={f.text} className="ma-auth-feature-item">
                <span>{f.icon}</span>
                <span>{f.text}</span>
              </div>
            ))}
          </div>

          {/* Decorative arch */}
          <div className="ma-auth-arch" aria-hidden="true" />
        </div>
      </div>

      {/* Right panel — form */}
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

          <h1 className="ma-auth-heading">Welcome Back</h1>
          <p className="ma-auth-sub">Sign in to continue your learning journey</p>

          {error && (
            <div className="ma-auth-error" role="alert">
              <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="ma-auth-form" noValidate>
            <div className="ma-form-group">
              <label htmlFor="login-email">Email Address</label>
              <div className="ma-input-icon-wrap">
                <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <input
                  id="login-email"
                  type="email"
                  placeholder="you@email.com"
                  required
                  value={form.email}
                  onChange={e => setForm({ ...form, email: e.target.value })}
                />
              </div>
            </div>

            <div className="ma-form-group">
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <label htmlFor="login-pass">Password</label>
                <a href="#" className="ma-auth-forgot">Forgot password?</a>
              </div>
              <div className="ma-input-icon-wrap">
                <svg aria-hidden="true" className="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input
                  id="login-pass"
                  type="password"
                  placeholder="Enter your password"
                  required
                  value={form.password}
                  onChange={e => setForm({ ...form, password: e.target.value })}
                />
              </div>
            </div>

            <div className="ma-auth-remember">
              <label className="ma-checkbox-label">
                <input
                  type="checkbox"
                  checked={form.remember}
                  onChange={e => setForm({ ...form, remember: e.target.checked })}
                />
                <span className="ma-checkmark" />
                Remember me for 30 days
              </label>
            </div>

            <button type="submit" className="ma-auth-submit" disabled={loading}>
              {loading ? (
                <span className="ma-spinner" aria-label="Signing in…" />
              ) : (
                'Sign In'
              )}
            </button>
          </form>

          <div className="ma-auth-divider"><span>or continue with</span></div>

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

          <p className="ma-auth-switch">
            Don't have an account?{' '}
            <Link href="/register" className="ma-auth-link">Create a free account →</Link>
          </p>
        </div>
      </div>
    </div>
  );
}
