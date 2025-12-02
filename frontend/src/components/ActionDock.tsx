import { Gamepad2, HeartHandshake, Map, Rocket } from 'lucide-react';

const actions = [
  { icon: Rocket, label: 'Boost', detail: 'x2 visibilité', highlight: true },
  { icon: Gamepad2, label: 'Quêtes', detail: '5 nouvelles' },
  { icon: HeartHandshake, label: 'Matches', detail: '12 connectés' },
  { icon: Map, label: 'Events', detail: 'Paris, Lyon' }
];

export function ActionDock() {
  return (
    <section className="floating-dock glass-card">
      {actions.map(({ icon: Icon, label, detail, highlight }) => (
        <button key={label} className={`dock-action ${highlight ? 'primary' : ''}`}>
          <Icon size={18} />
          <div>
            <span>{label}</span>
            <small>{detail}</small>
          </div>
        </button>
      ))}
    </section>
  );
}

