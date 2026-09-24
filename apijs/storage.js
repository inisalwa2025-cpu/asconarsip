import { initializeApp, getApps } from "https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js";
import {
  deleteObject,
  getDownloadURL,
  getStorage,
  listAll,
  ref,
  uploadBytes
} from "https://www.gstatic.com/firebasejs/12.19.0/firebase-storage.js";
import { firebaseConfig } from "./config.js";

const firebaseApp = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
export const storage = getStorage(firebaseApp);

export function storageRef(path) {
  return ref(storage, path);
}

export async function uploadToStorage(file, path) {
  if (!file) {
    throw new Error("File belum dipilih.");
  }

  const safePath = path || `uploads/${Date.now()}_${file.name}`;
  const fileRef = storageRef(safePath);
  await uploadBytes(fileRef, file);

  return {
    path: safePath,
    url: await getDownloadURL(fileRef)
  };
}

export async function getFileUrl(path) {
  return getDownloadURL(storageRef(path));
}

export async function deleteFromStorage(path) {
  if (!path) return false;
  await deleteObject(storageRef(path));
  return true;
}

export async function listFiles(folderPath = "") {
  const folderRef = storageRef(folderPath || "");
  const result = await listAll(folderRef);

  return {
    prefixes: result.prefixes,
    files: result.items.map((item) => ({
      name: item.name,
      fullPath: item.fullPath,
      path: item.fullPath
    }))
  };
}
