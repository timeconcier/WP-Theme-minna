/** ******************************************************************************
 * フィルター表示/非表示
 ****************************************************************************** */
const btnShowFilter = document.getElementsByName("show-filter");
const iconShowFilter = document.getElementById("icon-filter-caret");
const areaFilter = document.getElementById("filter-area");
if (btnShowFilter.length && areaFilter) {
  btnShowFilter[0].addEventListener("click", () => {
    if (iconShowFilter.classList[1] === "bi-caret-down-fill") {
      iconShowFilter.classList.remove("bi-caret-down-fill");
      iconShowFilter.classList.add("bi-caret-up-fill");
    } else {
      iconShowFilter.classList.add("bi-caret-down-fill");
      iconShowFilter.classList.remove("bi-caret-up-fill");
    }
    areaFilter.classList.toggle("d-none");
  });
}

/** ******************************************************************************
 * フィルター内アクション
 ****************************************************************************** */
// 給与
const f_sly = document.getElementsByName("f_sly");
const f_amt = document.getElementsByName("f_amt");
if (f_sly.length) {
  f_sly[0].addEventListener("change", () => {
    f_amt[0].disabled = f_sly[0].value ? false : true;
    f_amt[0].placeholder = f_sly[0].value ? "金額を入力してください。" : "給与形態を選択してください。";
  });
}

/** ******************************************************************************
 * シェアボタン
 ****************************************************************************** */
const btnShare = document.getElementsByClassName("share-button");
if (btnShare.length) {
  const array_btnShare = Array.from(btnShare);
  array_btnShare.forEach((el, i) => {
    btnShare[i].addEventListener("click", async () => {
      try {
        await navigator.share({ url: permalink });
      } catch (err) {
        console.error("Error: " + err);
      }
    });
  });
}

/** ******************************************************************************
 * Swiper.js
 ****************************************************************************** */
const front_swiper = document.getElementById("front-swiper");
if (front_swiper) {
  new Swiper("#front-swiper", {
    speed: 1000,
    effect: "fade",
    loop: true,
    navigation: {
      //ナビゲーションのオプション（矢印ボタンの要素を指定）
      // nextEl: '.swiper-button-next',
      // prevEl: '.swiper-button-prev',
    },
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true
    }
  });
}
