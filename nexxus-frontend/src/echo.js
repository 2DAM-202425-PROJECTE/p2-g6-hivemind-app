import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  authEndpoint: 'http://localhost:8000/broadcasting/auth',
  broadcaster: 'reverb',
  key: "rhymuvs4zyt8qnajgilh",
  wsHost: "localhost",
  wsPort: 8080,
  forceTLS: false,
  enabledTransports: ['ws'],
});
