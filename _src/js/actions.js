const scrollShowNav = document.getElementById("scroll-show-nav");
const scrollShowToTop = document.getElementById("scroll-show-toTop");
scrollShowHide(scrollShowNav, 350);
scrollShowHide(scrollShowToTop, 100);

window.onscroll = () => {
  scrollShowHide(scrollShowNav, 350, window.scrollY);
  scrollShowHide(scrollShowToTop, 100, window.scrollY);
};

// getThisPositionGPS()
/** *******************************************************************
 * スクロール量によって要素を表示/非表示させる
 * @param {object} el - 要素
 * @param {number} borderVal - 表示/非表示させる境界の値
 * @param {number} scrollVal - スクロール量
 ******************************************************************* */
function scrollShowHide(el, borderVal, scrollVal) {
  if (!el) return;
  scrollVal = scrollVal || window.scrollY;

  if (scrollVal > borderVal) {
    el.classList.remove("d-none");
    el.classList.add("d-block");
  } else {
    el.classList.add("d-none");
    el.classList.remove("d-block");
  }
}

// 位置情報取得
async function getThisPositionGPS() {
  let position = await new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });

  console.log(position.coords.latitude);
  console.log(position.coords.longitude);
}

// tippy('[data-tippy-content]',{
//     allowHTML: true,
// });

var tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});

// header.php scriptから取得
jQuery($ => {
  if (postType) {
    /** ******************************
         * 投稿タイプによる
         * フィルタータブ切り替え
         ****************************** */
    if ($("#filters-tab").length && $("#filters-tabContent").length) {
      $(".nav-item").map(i => {
        if ($(".nav-link").eq(i).hasClass("active"))
          $(".nav-link").eq(i).removeClass("active");
      });
      $(`.nav-item #pills-${postType}-tab`).addClass("active");

      $(".tab-pane").map(i => {
        if ($(".tab-pane").eq(i).hasClass("show active"))
          $(".tab-pane").eq(i).removeClass("show active");
      });
      $(`#filters-tabContent #pills-${postType}`).addClass("show active");
    }
  }

  /** ==========================================================================================================
     * ***********************************************************************************************************
     * 絞り込み検索制御
     * ***********************************************************************************************************
     * ======================================================================================================== */
  if (location.search) {
    const params = decodeURI(location.search).slice(1).split("&");
    const objParams = {};
    params.forEach(prm => {
      let param = prm.split("=");
      if (param[0].endsWith("[]")) {
        let arrKey = param[0].slice(0, -2);
        if (!Object.keys(objParams).includes(arrKey)) {
          objParams[arrKey] = [];
        }
        objParams[arrKey].push(decodeURI(param[1]));
      } else {
        objParams[param[0]] = decodeURI(param[1]).replace(/%3A/g, ":");
      }
    });

    console.log(objParams)
    for (key in objParams) {
      if (Array.isArray(objParams[key])) {
        let vals = objParams[key];
        vals.forEach(v => {
          let el = $(`form [name="${key}[]"][value="${v}"]`);
          switch (key) {
            case "g_ep":
              el.parent().removeClass("bg-outline-success");
              el.parent().addClass("bg-success");
              break;
          }
          el.prop("checked", true);
        });
      } else {
        let val = objParams[key];
        if (key == 's') {
          if (val) val = val.replace(/\+/g, ' ')
        }
        let el = $(`form [name="${key}"]`);
        el.eq(0).val(val);
      }
    }
  }

  /** *****************************************************
     * 絞り込み検索制御
     * - まち：カテゴリー
     ***************************************************** */
  $(`label.g-ep-label`).off();
  $(`label.g-ep-label`).on("click", function() {
    const checkbox = $(this).find('input[type="checkbox"]').prop("checked");
    if (checkbox) {
      // チェックしたとき
      $(this).removeClass("bg-outline-success");
      $(this).addClass("bg-success");
    } else {
      // チェックが外れたとき
      $(this).removeClass("bg-success");
      $(this).addClass("bg-outline-success");
    }
    console.log();
    // $(this).hasClass('bg-outline-success')
  });

  const salary_type = $(`#form-${postType} select[name="f_sly"]`);
  if (salary_type.val()) {
    $(`#form-${postType} input[name="f_amt"]`).prop("disabled", false);
    $(`#form-${postType} input[name="f_amt"]`).prop("placeholder", "");
  }
  salary_type.on("change", function() {
    if ($(this).eq(0).val()) {
      $(`#form-${postType} input[name="f_amt"]`).prop("disabled", false);
      $(`#form-${postType} input[name="f_amt"]`).prop("placeholder", "");
    } else {
      $(`#form-${postType} input[name="f_amt"]`).prop("disabled", true);
      $(`#form-${postType} input[name="f_amt"]`).prop(
        "placeholder",
        "給与形態を選択してください。"
      );
    }
  });

  /** *******************************************************************
     * 検索文字列が空の場合、Enterキー無効
     ******************************************************************* */
  // $('#form').keypress(function (e) {
  //     if (e.which && e.which === 13 || e.keyCode && e.keyCode === 13) {
  //         if (!$(this).val() || $(this).val().match(/\S/g)) {
  //             return false;
  //         }
  //     }
  // });
});
