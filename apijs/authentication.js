import { initializeApp, getApps } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js";
import {
  browserLocalPersistence,
  createUserWithEmailAndPassword,
  deleteUser,
  getAuth,
  onAuthStateChanged,
  sendEmailVerification,
  sendPasswordResetEmail,
  setPersistence,
  signInWithEmailAndPassword,
  signOut
} from "https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js";
import { firebaseConfig } from "./config.js";

const firebaseApp = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
const auth = getAuth(firebaseApp);

await setPersistence(auth, browserLocalPersistence);
const authReady = new Promise((resolve) => {
  const unsubscribe = onAuthStateChanged(auth, (user) => {
    unsubscribe();
    resolve(user);
  });
});

const firebaseErrorMessages = {
  "auth/email-already-in-use": "Email sudah terdaftar.",
  "auth/invalid-credential": "Email atau password tidak benar.",
  "auth/invalid-email": "Format email belum benar.",
  "auth/operation-not-allowed": "Login Email/Password belum diaktifkan di Firebase Console.",
  "auth/network-request-failed": "Koneksi ke Firebase gagal. Periksa internet Anda.",
  "auth/too-many-requests": "Terlalu banyak percobaan. Coba lagi nanti.",
  "auth/user-not-found": "Akun dengan email tersebut tidak ditemukan.",
  "auth/weak-password": "Password terlalu lemah.",
  "auth/wrong-password": "Password tidak benar.",
  "auth/email-not-verified": "Email belum diverifikasi. Buka tautan verifikasi di inbox Anda.",
  "auth/email-already-verified": "Email sudah diverifikasi. Silakan masuk ke dashboard.",
  "auth/no-current-user": "Sesi akun tidak ditemukan. Kembali ke register lalu kirim ulang email.",
  "auth/quota-exceeded": "Batas pengiriman email Firebase tercapai. Coba lagi nanti.",
  "auth/internal-error": "Firebase gagal memproses pengiriman email. Coba lagi nanti.",
  "auth/invalid-api-key": "API key Firebase tidak valid. Periksa config.js.",
  "auth/app-not-authorized": "Aplikasi ini belum diizinkan oleh Firebase project.",
  "auth/invalid-continue-uri": "Alamat tautan verifikasi tidak valid. Periksa Authorized domains di Firebase.",
  "auth/missing-continue-uri": "Alamat tautan verifikasi belum dikonfigurasi.",
  "auth/unauthorized-continue-uri": "Alamat website belum diizinkan di Firebase Console.",
  "auth/unauthorized-domain": "Domain website belum diizinkan di Firebase Console.",
  "auth/missing-email": "Email wajib diisi."
};

export function getActionSettings(mode = "verifyEmail") {
  const targetPage = mode === "verifyEmail" ? "./index.html" : "./authentication.html";

  if (typeof window === "undefined") {
    return {
      url: "http://localhost/arsipin/asconarsip/" + targetPage.replace("./", "") + "?mode=" + encodeURIComponent(mode),
      handleCodeInApp: false
    };
  }

  const redirectUrl = new URL(targetPage, window.location.href);
  redirectUrl.searchParams.set("mode", mode);

  return {
    url: redirectUrl.href,
    handleCodeInApp: false
  };
}

export function authMessage(error) {
  return firebaseErrorMessages[error?.code] || "Permintaan autentikasi gagal. Coba lagi.";
}

export async function registerAdmin(email, password) {
  if (!email.trim()) {
    const error = new Error("Email wajib diisi.");
    error.code = "auth/missing-email";
    throw error;
  }

  const credential = await createUserWithEmailAndPassword(auth, email.trim(), password);
  await sendEmailVerification(credential.user, getActionSettings("verifyEmail"));
  await signOut(auth);
  return credential.user;
}

export async function registerOrResendVerification(email, password) {
  try {
    return await registerAdmin(email, password);
  } catch (error) {
    if (error.code !== "auth/email-already-in-use") throw error;
    const credential = await signInWithEmailAndPassword(auth, email.trim(), password);
    await credential.user.reload();
    if (credential.user.emailVerified) {
      await signOut(auth);
      const verifiedError = new Error("Email sudah diverifikasi. Silakan login.");
      verifiedError.code = "auth/email-already-verified";
      throw verifiedError;
    }
    await sendEmailVerification(credential.user, getActionSettings("verifyEmail"));
    await signOut(auth);
    return credential.user;
  }
}

export async function deleteUnverifiedAccount() {
  const user = await authReady;
  if (!user) return false;
  await user.reload();
  if (user.emailVerified) return false;
  await deleteUser(user);
  return true;
}

export async function loginAdmin(email, password) {
  const credential = await signInWithEmailAndPassword(auth, email.trim(), password);
  if (!credential.user.emailVerified) {
    await signOut(auth);
    const error = new Error("Email belum diverifikasi.");
    error.code = "auth/email-not-verified";
    throw error;
  }
  return credential.user;
}

export async function resendVerificationEmail() {
  const user = await authReady;
  if (!user) {
    const error = new Error("Sesi akun tidak ditemukan.");
    error.code = "auth/no-current-user";
    throw error;
  }
  await sendEmailVerification(user, getActionSettings("verifyEmail"));
}

export function sendResetEmail(email) {
  return sendPasswordResetEmail(auth, email.trim(), getActionSettings("forgot"));
}

export function logoutAdmin() {
  return signOut(auth);
}

export function watchAuthState(callback) {
  return onAuthStateChanged(auth, callback);
}

export function currentUser() {
  return auth.currentUser;
}

export { auth, firebaseApp };
