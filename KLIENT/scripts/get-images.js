"use strict";
let loggedInUser = "2";
async function getImages() {
  try {
    let response = await fetch(`http://localhost:4000/get-images.php?user_posts=${loggedInUser}&includes=1`);
    let data = await response.json();
    console.log(data);
    return data;
  } catch (error) {
    console.error(error);
  }
}

async function appendImages() {
  const images = await getImages();
  console.log(images);
  let itteration = 0;
  images.forEach((imageInfo) => {
    let post = createPost(imageInfo);
    document.getElementById(`images-${itteration % 3}`).append(post);
    itteration += 1;
  });
}

function createPost(imageInfo) {
  console.log(imageInfo);
  let grid = document.createElement("div");
  grid.className = "post grid grid-cols-1 grid-rows-1";

  let info = document.createElement("div");
  info.className =
    "post w-40 h-full row-start-1 col-start-1 hover:bg-gray-900 transition-colors duration-300 hover:opacity-25";
  let img = document.createElement("img");
  img.className = "box-content w-40  row-start-1 col-start-1";
  img.setAttribute("src", imageInfo["image_url"]);

  grid.append(img, info);
  return grid;
}

appendImages();
