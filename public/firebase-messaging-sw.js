importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyCAxhJpDosDlvFbwMV-bTT_rrPvgHWLPyQ",
  authDomain: "fir-6b235.firebaseapp.com",
  projectId: "fir-6b235",
  storageBucket: "fir-6b235.appspot.com", // ✅ FIXED
  messagingSenderId: "838336838212",
  appId: "1:838336838212:web:ef56d1b7501a3cdc9c1a11",
  measurementId: "G-JDTF1MP6TH"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);

  self.registration.showNotification(
    payload.notification.title,
    {
      body: payload.notification.body,
      icon: '/favicon.ico'
    }
  );
});
