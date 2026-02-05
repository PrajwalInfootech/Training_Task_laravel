import './bootstrap';
/*
  Add custom scripts here
*/
import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);
import { requestFcmToken } from "./firebase";

requestFcmToken().then(token => {
  if (!token) return;

  fetch('/save-fcm-token', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document
        .querySelector('meta[name="csrf-token"]').content
    },
    credentials: 'same-origin',
    body: JSON.stringify({ token })
  });
});
