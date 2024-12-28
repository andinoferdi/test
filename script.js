document.getElementById("refresh-button").addEventListener("click", () => {
    const images = document.querySelectorAll(".car img");
    images.forEach(img => {
        const url = new URL(img.src);
        const searchTerm = url.searchParams.get("car");
        img.src = `https://source.unsplash.com/400x300/?car,${searchTerm}&random=${Math.random()}`;
    });
});
