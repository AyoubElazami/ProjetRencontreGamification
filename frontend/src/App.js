import { jsx as _jsx, jsxs as _jsxs, Fragment as _Fragment } from "react/jsx-runtime";
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
    return (_jsxs(_Fragment, { children: [_jsx(MobileNav, {}), _jsxs("main", { children: [_jsx(Hero, {}), _jsx(AuthPanel, {}), _jsx(ActionDock, {}), _jsx(SwipeDeck, {}), _jsx(ProfileSpotlight, {}), _jsx(MatchGrid, {}), _jsx(QuestTimeline, {}), _jsx(LiveRooms, {}), _jsx(ExperienceMetrics, {}), _jsx(Testimonials, {})] })] }));
}
export default App;
