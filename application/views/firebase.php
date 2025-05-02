<script src="https://www.gstatic.com/firebasejs/10.8.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.8.1/firebase-messaging-compat.js"></script>

<script>
    const firebaseConfig = {
      apiKey: "AIzaSyBTnTuQX3NDogDmo8cy9btNZkWKq9K9GM8",
      authDomain: "yortech-id.firebaseapp.com",
      projectId: "yortech-id",
      storageBucket: "yortech-id.firebasestorage.app",
      messagingSenderId: "759217141971",
      appId: "1:759217141971:web:a7dbd53d608cebab78fc26",
      measurementId: "G-C4M4BY9PNN"
    };


    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then((registration) => {
                console.log("✅ Service Worker registered:", registration.scope);
                requestNotificationPermission(registration);
            })
            .catch((error) => console.error("❌ Service Worker registration failed:", error));
    }

    function requestNotificationPermission(registration) {
        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                console.log("✅ Notification permission granted.");
                getFCMToken(registration);
            } else if (permission === "denied") {
                alert("❌ You blocked notifications. Please enable them in browser settings.");
                watchPermissionChange(registration);
            }
        });
    }

    function watchPermissionChange(registration) {
        let interval = setInterval(() => {
            if (Notification.permission === "granted") {
                clearInterval(interval);
                console.log("🔄 Permission changed to GRANTED.");
                getFCMToken(registration);
            }
        }, 3000);
    }

    function getFCMToken(registration) {
        messaging.getToken({
                vapidKey: "BDDa89SDooWX0HBgiCGCi_F8o8voSRP-P7d_k9OMeYwTxRaRq05gvOpwIJArl58bexLvdpydiutD-Inf4MrqBig",
                serviceWorkerRegistration: registration
            })
            .then((currentToken) => {
                if (currentToken) {
                    console.log("📲 FCM Token:", currentToken);
                    if(document.getElementById("fcm_token")) document.getElementById("fcm_token").value = currentToken; 
                } else {
                    console.warn("⚠️ No registration token available.");
                }
            }).catch((error) => {
                console.error("❌ Error retrieving token:", error);
            });
    }

    messaging.onMessage((payload) => {
        console.log("📩 Message received in foreground:", payload);
        new Notification(payload.notification.title, {
            body: payload.notification.body,
            icon: payload.notification.icon
        });
    });
</script>