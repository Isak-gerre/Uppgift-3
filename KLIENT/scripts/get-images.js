"use strict";

async function getImages() {
  try {
    let response = await fetch("http://localhost:4000/get-images.php", {
      credentials: "include",
      method: "GET",
      headers: { "Content-Type": "application/json" },
    });
    let data = await response.json();
    console.log(data.posts);
    return data.posts;
  } catch (error) {
    console.error(error);
  }
}

getImages();
