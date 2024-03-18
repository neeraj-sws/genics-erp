importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey:  "AIzaSyCh_oL4CCPB_TIpzeCzSvKxZgx2YtjR0-c",
    projectId: "erp-genics",
    messagingSenderId: "406824230315",
    appId: "1:406824230315:web:3675291babaf0f158406ee",
});
self.addEventListener('notificationclick', event => {
    
  const url = event.notification.data.FCM_MSG.data.click_action
  
  clients.openWindow(url);
  
})

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
    
    const notificationOptions = {
    body: payload.data.body,    
  };
  return self.registration.showNotification(payload.data.title, notificationOptions);
});