import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { motion } from 'framer-motion';
import { Flame, Heart, Sparkles, Star, X } from 'lucide-react';
import { deckProfiles } from '../assets/mockData';
const cardVariants = {
    initial: (index) => ({
        rotate: index === 0 ? 0 : index === 1 ? -4 : 4,
        y: index * -12,
        zIndex: deckProfiles.length - index
    }),
    animate: {
        rotate: 0,
        y: 0,
        transition: { type: 'spring', duration: 0.5, damping: 20 }
    }
};
export function SwipeDeck() {
    return (_jsxs("section", { className: "glass-card swipe-section", children: [_jsxs("header", { className: "section-header", children: [_jsxs("div", { children: [_jsx("p", { children: "Deck immersif" }), _jsx("h2", { children: "Swipe sc\u00E9naris\u00E9 avec actions exclusives" })] }), _jsxs("div", { className: "pill neon", children: [_jsx(Flame, { size: 16 }), "Boost actif 12min"] })] }), _jsx("div", { className: "swipe-deck", children: deckProfiles.map((profile, index) => (_jsxs(motion.article, { className: "swipe-card", custom: index, variants: cardVariants, initial: "initial", animate: "animate", whileHover: { y: -6 }, children: [_jsx("div", { className: "swipe-card__image", style: { backgroundImage: `url(${profile.image})` }, children: _jsxs("div", { className: "swipe-card__badge", children: [_jsx(Star, { size: 16 }), profile.compatibility, "% vibe match"] }) }), _jsxs("div", { className: "swipe-card__body", children: [_jsx("h3", { children: profile.name }), _jsx("p", { children: profile.headline }), _jsxs("div", { className: "swipe-card__meta", children: [_jsx("span", { children: profile.distance }), _jsxs("span", { children: ["Compat ", profile.compatibility, "%"] })] }), _jsx("div", { className: "tag-wrap", children: profile.tags.map((tag) => (_jsx("span", { className: "tag-pill", children: tag }, tag))) })] })] }, profile.name))) }), _jsxs("div", { className: "swipe-actions", children: [_jsxs("button", { className: "btn ghost", children: [_jsx(X, { size: 18 }), "Passer"] }), _jsxs("button", { className: "btn primary", children: [_jsx(Heart, { size: 18 }), "Matcher"] }), _jsxs("button", { className: "btn accent", children: [_jsx(Sparkles, { size: 18 }), "Lancer une qu\u00EAte"] })] })] }));
}
