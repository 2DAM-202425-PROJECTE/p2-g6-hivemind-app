// src/utils/nameEffects.js
export const getNameEffectClass = (path) => {
  if (!path) return '';
  if (path.includes('lamp.svg')) return 'soft-glow';
  if (path.includes('blend.svg')) return 'gradient-fade';
  if (path.includes('badge.svg')) return 'golden-outline';
  if (path.includes('vibrate.svg')) return 'dark-pulse';
  if (path.includes('stars.svg')) return 'cosmic-shine';
  if (path.includes('lightbulb.svg')) return 'neon-edge';
  if (path.includes('snowflake.svg')) return 'frost-glow';
  if (path.includes('flame.svg')) return 'fire-flicker';
  if (path.includes('gem.svg')) return 'emerald-sheen';
  if (path.includes('cloud-fog.svg')) return 'phantom-haze';
  if (path.includes('zap.svg')) return 'electric-glow';
  if (path.includes('moon.svg')) return 'lunar-haze';
  if (path.includes('sun.svg')) return 'solar-flare';
  if (path.includes('waves.svg')) return 'wave-shimmer';
  if (path.includes('diamond.svg')) return 'crystal-pulse';
  if (path.includes('rainbow.svg')) return 'rainbow-gleam';
  return '';
};
