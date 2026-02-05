import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";


const firebaseConfig = {
  apiKey: "AIzaSyCAxhJpDosDlvFbwMV-bTT_rrPvgHWLPyQ",
  authDomain: "fir-6b235.firebaseapp.com",
  projectId: "fir-6b235",
  storageBucket: "fir-6b235.firebasestorage.app",
  messagingSenderId: "838336838212",
  appId: "1:838336838212:web:ef56d1b7501a3cdc9c1a11",
  measurementId: "G-JDTF1MP6TH"
};
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

export const requestFcmToken = async () => {
  try {
    const permission = await Notification.requestPermission();
    if (permission !== "granted") return null;

    return await getToken(messaging, {
      vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
    });
  } catch (err) {
    console.error("FCM token error", err);
    return null;
  }
};
const showInAppToast = (title, body) => {
  // Check if a toast already exists to prevent stacking messily
  const existingToast = document.getElementById("fcm-toast");
  if (existingToast) existingToast.remove();

  const toast = document.createElement("div");
  toast.id = "fcm-toast";
  
  Object.assign(toast.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    backgroundColor: "#1a1a1a", // Slightly sleeker dark
    color: "#fff",
    padding: "16px",
    borderRadius: "12px",
    boxShadow: "0 10px 15px -3px rgba(0,0,0,0.3)",
    zIndex: "9999",
    minWidth: "280px",
    fontFamily: "system-ui, -apple-system, sans-serif",
    borderLeft: "4px solid #f39c12", // Accent color
    cursor: "pointer",
    animation: "fadeIn 0.5s"
  });

  toast.innerHTML = `
    <div style="font-weight: 600; font-size: 15px;">${title}</div>
    <div style="font-size: 13px; margin-top: 4px; color: #ccc;">${body}</div>
  `;

  toast.onclick = () => toast.remove();
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateY(-20px)";
    toast.style.transition = "opacity 0.4s, transform 0.4s";
    setTimeout(() => toast.remove(), 400);
  }, 6000);
};

// SINGLE Listener - This is all you need
onMessage(messaging, (payload) => {
  console.log("🔥 FCM Message Received:", payload);

  const title = payload?.notification?.title || "Notification";
  const body = payload?.notification?.body || "";

  if (document.visibilityState === "visible") {
    // 1. Tab is active: Use the Toast (Best UX)
    showInAppToast(title, body);
  } else {
    // 2. Tab is in background: Try to trigger a System Notification
    // Note: Usually the Service Worker handles this, but this is a backup
    if (Notification.permission === "granted") {
      new Notification(title, {
        body: body,
        icon: "/favicon.ico",
      });
    }
  }
});