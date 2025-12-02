import { experiences } from '../assets/mockData';

export function ExperienceMetrics() {
  return (
    <section className="glass-card" style={{ display: 'grid', gap: '1rem' }}>
      <header>
        <p style={{ color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }}>Impact communauté</p>
        <h2 style={{ fontSize: '1.75rem', marginTop: '0.25rem' }}>Des chiffres qui parlent</h2>
      </header>
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))',
          gap: 'var(--grid-gap)'
        }}
      >
        {experiences.map((exp) => (
          <article key={exp.label} className="glass-card" style={{ textAlign: 'center' }}>
            <p style={{ fontSize: '2rem', fontWeight: 700 }}>{exp.value}</p>
            <p style={{ color: 'var(--text-muted)', marginBottom: '0.5rem' }}>{exp.label}</p>
            <span style={{ color: 'var(--accent)', fontWeight: 600 }}>{exp.trend}</span>
          </article>
        ))}
      </div>
    </section>
  );
}

