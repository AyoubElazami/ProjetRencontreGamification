import { questSteps } from '../assets/mockData';

export function QuestTimeline() {
  return (
    <section className="glass-card quest-timeline">
      <header className="section-header">
        <div>
          <p>Flow gamifié</p>
          <h2>La quête qui te fait matcher différemment</h2>
        </div>
        <span className="pill ghost">Mode story-driven</span>
      </header>
      <div className="timeline">
        {questSteps.map((step, index) => (
          <article key={step.title} className="timeline-step">
            <div className="timeline-index">{index + 1}</div>
            <div>
              <h3>{step.title}</h3>
              <p>{step.description}</p>
              <small>{step.eta}</small>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

