import { Bell, ShieldCheck, UserRound } from 'lucide-react';
import { motion } from 'framer-motion';
import { menuTabs } from '../assets/mockData';

export function MobileNav() {
  return (
    <motion.nav
      className="glass-card hud-nav"
      initial={{ opacity: 0, y: -10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5 }}
    >
      <div className="hud-brand">
        <div>
          <p>Rencontre+</p>
          <strong>Edition 2025</strong>
        </div>
        <span className="status-pill">Communauté privée</span>
      </div>

      <div className="menu-tabs">
        {menuTabs.map((tab) => (
          <button key={tab.label} className={`menu-tab ${tab.active ? 'active' : ''}`}>
            <span>{tab.label}</span>
            {tab.status ? <small>{tab.status}</small> : null}
          </button>
        ))}
      </div>

      <div className="hud-actions">
        <button className="badge ghost">
          <ShieldCheck size={16} />
          Safe mode
        </button>
        <button className="icon-button">
          <Bell size={16} />
        </button>
        <div className="avatar-badge">
          <UserRound size={16} />
          <span>Toi</span>
        </div>
      </div>
    </motion.nav>
  );
}
