import { featuredProfiles } from '../assets/mockData';

export function ProfileSpotlight() {
  return (
    <section className="glass-card">
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.25rem' }}>
        <div>
          <p style={{ color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }}>Spotlight temps réel</p>
          <h2 style={{ fontSize: '1.75rem', marginTop: '0.25rem' }}>Profils compatibles autour de toi</h2>
        </div>
        <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>Basé sur ton mood du jour</p>
      </div>

      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))',
          gap: 'var(--grid-gap)'
        }}
      >
        {featuredProfiles.map((profile) => (
          <article
            key={profile.name}
            className="glass-card"
            style={{
              padding: '0',
              overflow: 'hidden',
              borderRadius: '1.5rem',
              display: 'flex',
              flexDirection: 'column'
            }}
          >
            <div
              style={{
                aspectRatio: '4 / 5',
                backgroundImage: `url(${profile.image})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center'
              }}
            />
            <div style={{ padding: '1.25rem' }}>
              <h3 style={{ fontSize: '1.25rem' }}>{profile.name}</h3>
              <p style={{ color: 'var(--text-muted)' }}>{profile.location}</p>
              <div
                style={{
                  marginTop: '1rem',
                  display: 'flex',
                  justifyContent: 'space-between',
                  alignItems: 'center'
                }}
              >
                <span
                  style={{
                    padding: '0.4rem 0.9rem',
                    borderRadius: '999px',
                    background: 'rgba(255,255,255,0.08)',
                    fontSize: '0.9rem'
                  }}
                >
                  {profile.vibe}
                </span>
                <span style={{ fontWeight: 700, fontSize: '1.3rem' }}>{profile.score}%</span>
              </div>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

