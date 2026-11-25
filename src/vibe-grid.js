const url = "https://kitsu.io/api/edge/anime?sort=-userCount&page[limit]=20&include=genres";

const searchForm = document.querySelector("#search-form");
const searchInput = document.querySelector("#search-input");
const auraButtons = document.querySelector("#aura-buttons");
const auraDesc = document.querySelector("#aura-desc");
const animeGrid = document.querySelector("#anime-grid");
const loadingMessage = document.querySelector("#loading-message");
const errorMessage = document.querySelector("#error-message");
const resultCount = document.querySelector("#result-count");

let animeData = [];
let activeAura = "semua";

function punyaGenre(anime, daftar) {
  const ketemu = daftar.filter((nama) => anime.genres.find((g) => g === nama));
  return ketemu.length > 0;
}

const auras = [
  { id: "semua", label: "Semua", desc: "Semua judul.", cocok: () => true },
  {
    id: "ngakak",
    label: "Ngakak",
    cocok: (a) => punyaGenre(a, ["Comedy"]),
  },
  {
    id: "baper",
    label: "Bikin Baper",
    cocok: (a) => punyaGenre(a, ["Romance", "Drama"]),
  },
  {
    id: "mikir",
    label: "Mikir Keras",
    cocok: (a) => punyaGenre(a, ["Mystery", "Thriller", "Psychological", "Science Fiction"]),
  },
  {
    id: "skor-tinggi",
    label: "Skor Tinggi",
    cocok: (a) => parseFloat(a.score) >= 8,
  },
];

function escapeHTML(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

function renderAura() {
  let html = "";

  auras.forEach((aura) => {
    const warna =
      aura.id === activeAura
        ? "bg-flip-600 text-white border-flip-600"
        : "bg-white border-flip-100 hover:border-flip-600";
    html += `<button data-aura="${aura.id}" class="rounded-md border px-4 py-1.5 text-sm ${warna}">${aura.label}</button>`;
  });

  auraButtons.innerHTML = html;
  auraDesc.textContent = auras.find((a) => a.id === activeAura).desc;
}

function renderAnime(list) {
  resultCount.textContent = `${list.length} judul`;

  if (list.length === 0) {
    animeGrid.innerHTML = `<p class="col-span-full py-10 text-center">Judul tidak ditemukan.</p>`;
    return;
  }

  let html = "";

  list.forEach((anime) => {
    const judul = escapeHTML(anime.title);
    const eps = anime.episodes ?? "?";

    let genre = "";
    anime.genres.forEach((g, i) => {
      if (i < 3) {
        genre += i > 0 ? ", " + g : g;
      }
    });

    html += `
      <article class="overflow-hidden rounded-md border border-flip-100 bg-white">
        <img src="${anime.image}" alt="${judul}" loading="lazy"
             class="h-64 w-full object-cover">
        <div class="p-4">
          <h2 class="mb-1 font-semibold">${judul}</h2>
          <p class="text-sm text-flip-900/70">Skor ${anime.score} &middot; ${eps} eps</p>
          <p class="mt-1 text-sm text-flip-600">${genre}</p>
        </div>
      </article>
    `;
  });

  animeGrid.innerHTML = html;
}

function tampilkan() {
  const keyword = searchInput.value.toLowerCase();
  const aura = auras.find((a) => a.id === activeAura);

  const hasil = animeData
    .filter((anime) => aura.cocok(anime))
    .filter((anime) => anime.title.toLowerCase().includes(keyword));

  renderAnime(hasil);
}

function olahData(result) {
  const namaGenre = {};
  result.included.forEach((item) => {
    if (item.type === "genres") {
      namaGenre[item.id] = item.attributes.name;
    }
  });

  return result.data.map((item) => {
    const a = item.attributes;
    return {
      id: item.id,
      title: a.canonicalTitle,
      image: a.posterImage ? a.posterImage.small : "",
      score: a.averageRating ? (a.averageRating / 10).toFixed(1) : "-",
      episodes: a.episodeCount,
      genres: item.relationships.genres.data.map((g) => namaGenre[g.id]),
    };
  });
}

async function getAnime() {
  try {
    loadingMessage.textContent = "Loading data...";
    errorMessage.textContent = "";

    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Status " + response.status);
    }

    const result = await response.json();
    animeData = olahData(result);
    tampilkan();
  } catch (error) {
    errorMessage.textContent = "Data gagal dimuat. Coba lagi nanti.";
    console.error(error);
  } finally {
    loadingMessage.textContent = "";
  }
}

searchInput.addEventListener("input", tampilkan);

searchForm.addEventListener("submit", (event) => {
  event.preventDefault();
  tampilkan();
});

auraButtons.addEventListener("click", (event) => {
  const tombol = event.target.closest("button");
  if (!tombol) return;

  activeAura = tombol.dataset.aura;
  renderAura();
  tampilkan();
});

renderAura();
getAnime();