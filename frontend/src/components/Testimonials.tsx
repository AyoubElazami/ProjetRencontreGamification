import { testimonials } from '../assets/mockData';

export function Testimonials() {
  return (
    <section className="glass-card">
      <header style={{ marginBottom: '1.25rem' }}>
        <p style={{ color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }}>Retours bêta</p>
        <h2 style={{ fontSize: '1.75rem', marginTop: '0.25rem' }}>Ils ont testé, ils racontent</h2>
      </header>
      <div style={{ display: 'flex', flexDirection: 'column', gap: '1.25rem' }}>
        {testimonials.map((item) => (
          <blockquote
            key={item.author}
            className="glass-card"
            style={{ borderRadius: '1rem', fontSize: '1.1rem', lineHeight: 1.5 }}
          >
            <p style={{ color: 'var(--text-muted)' }}>{item.quote}</p>
            <cite style={{ display: 'block', marginTop: '0.75rem', fontWeight: 600 }}>{item.author}</cite>
          </blockquote>
        ))}
      </div>
    </section>
  );
}

