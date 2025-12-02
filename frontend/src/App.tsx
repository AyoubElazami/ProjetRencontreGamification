import { Hero } from './components/Hero';
import { ProfileSpotlight } from './components/ProfileSpotlight';
import { MatchGrid } from './components/MatchGrid';
import { ExperienceMetrics } from './components/ExperienceMetrics';
import { Testimonials } from './components/Testimonials';
import { MobileNav } from './components/MobileNav';
import { SwipeDeck } from './components/SwipeDeck';
import { QuestTimeline } from './components/QuestTimeline';
import { LiveRooms } from './components/LiveRooms';
import { ActionDock } from './components/ActionDock';
import { AuthPanel } from './components/AuthPanel';

function App() {
  return (
    <>
      <MobileNav />
      <main>
        <Hero />
        <AuthPanel />
        <ActionDock />
        <SwipeDeck />
        <ProfileSpotlight />
        <MatchGrid />
        <QuestTimeline />
        <LiveRooms />
        <ExperienceMetrics />
        <Testimonials />
      </main>
    </>
  );
}

export default App;

