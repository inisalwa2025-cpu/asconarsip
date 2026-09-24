import { applyActionCode, checkActionCode, getAuth } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-auth.js";
import { firebaseApp } from "./apijs/authentication.js";

const auth = getAuth(firebaseApp);
const params = new URLSearchParams(window.location.search);
const mode = params.get("mode");
const actionCode = params.get("oobCode");

if (mode === "verifyEmail" && actionCode) {
  try {
    const actionInfo = await checkActionCode(auth, actionCode);
    await applyActionCode(auth, actionCode);

    const loginUrl = new URL("./index.html", window.location.href);
    if (actionInfo.data.email) {
      loginUrl.searchParams.set("email", actionInfo.data.email);
      loginUrl.searchParams.set("verified", "1");
    }

    window.location.replace(loginUrl.href);
  } catch (error) {
    const errorUrl = new URL("./index.html", window.location.href);
    errorUrl.searchParams.set("status", "error");
    errorUrl.searchParams.set("reason", error.code || "unknown");
    window.location.replace(errorUrl.href);
  }
}
