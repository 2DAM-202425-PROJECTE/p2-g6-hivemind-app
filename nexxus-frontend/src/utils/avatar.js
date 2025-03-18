export const generateAvatar = (name) => {
  const canvas = document.createElement('canvas');
  canvas.width = 100;
  canvas.height = 100;
  const ctx = canvas.getContext('2d');
  ctx.fillStyle = '#FFDE21';
  ctx.fillRect(0, 0, 100, 100);

  const words = name.trim().split(' ');
  let initials = '';
  if (words.length === 1 && words[0].length >= 2) {
    initials = words[0].substring(0, 2);
  } else if (words.length >= 2) {
    initials = words[0][0] + words[words.length - 1][0];
  } else if (words.length === 1) {
    initials = words[0][0];
  }
  initials = initials.toUpperCase();
  ctx.fillStyle = '#000000';
  ctx.font = 'bold 48px Arial';
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText(initials, 50, 50);

  return canvas.toDataURL('image/png');
};
