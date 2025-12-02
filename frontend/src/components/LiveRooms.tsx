import { liveRooms } from '../assets/mockData';

export function LiveRooms() {
  return (
    <section className="glass-card live-rooms">
      <header className="section-header">
        <div>
          <p>Rooms audio/vidéo</p>
          <h2>Choisis ton ambiance, rejoins la scène</h2>
        </div>
        <span className="pill neon">Modération IA en temps réel</span>
      </header>
      <div className="rooms-grid">
        {liveRooms.map((room) => (
          <article key={room.title} className="room-card" style={{ background: room.gradient }}>
            <div className="room-card__head">
              <strong>{room.title}</strong>
              <span>{room.latency}</span>
            </div>
            <p>{room.mood}</p>
            <div className="room-card__foot">
              <span>{room.members} membres</span>
              <span>{room.distance}</span>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

