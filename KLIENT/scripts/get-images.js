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
  grid.className = "post mt-4 grid grid-cols-1 grid-rows-1 relative group shadow-xl";

  let info = document.createElement("div");
  info.className =
    "post w-40 h-full opacity-0 row-start-1 col-start-1 bg-gradient-to-b from-gray-800 to-gray-900 transition-color duration-300 hover:h-25 z-10 relative group-hover:opacity-75 group";

  let infoBox = document.createElement("div");
  infoBox.className = "absolute bottom-0 h-10 w-full text-white flex flex-row justify-around items-center";

  let likes = document.createElement("p");
  likes.textContent = imageInfo["total_likes"] + "🤍";
  likes.className = "text-sm";
  let date = document.createElement("p");
  date.textContent = imageInfo["date"];
  date.className = "text-sm";

  infoBox.append(likes, date);
  info.append(infoBox);
  let img = document.createElement("img");
  img.className = "box-content w-40  row-start-1 col-start-1 ";
  img.setAttribute("src", imageInfo["image_url"]);

  grid.append(img, info);
  return grid;
}

appendImages();
