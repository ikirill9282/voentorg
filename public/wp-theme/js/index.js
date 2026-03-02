document.addEventListener("DOMContentLoaded", function () {
 
  const menuItems = document.querySelectorAll(".header__bottom-left__menu-item")
  const menuBurgerItems = document.querySelectorAll(
    ".header__bottom-burger__menu-item"
  )
  const menu = document.querySelector(".header__bottom-burger__menu")
  const btnBurger = document.querySelector(".header__bottom-burger__img")
  const btnClose = document.querySelector(".header__bottom-burger__close")
  const pathname = document.location.pathname
  if (menuItems) {
    menuItems.forEach(p => {
      if (p.pathname === pathname) {
        p.classList.add("active")
      }
    })
  }
  menuBurgerItems.forEach(p => {
    if (p.pathname === pathname) {
      p.classList.add("active")
    }
    p.addEventListener("click", function () {
      btnBurger.style.cssText = "display: block"
      btnClose.style.cssText = "display: none"
      menu.classList.remove("open")
    })
  })

  btnBurger.addEventListener("click", function () {
    menu.classList.add("open")
    btnBurger.style.cssText = "display: none"
    btnClose.style.cssText = "display: block"
  })
  btnClose.addEventListener("click", function () {
    menu.classList.remove("open")
    btnBurger.style.cssText = "display: block"
    btnClose.style.cssText = "display: none"
  })
})
// document.querySelector(".main").addEventListener("click", function () {
//   document
//     .querySelector(".header__bottom-burger__menu")
//     .classList.remove("open")
// })
const search = document.querySelector(".header__bottom-right__search")
const searchModal = document.querySelector(".search-modal")
const searchModalCont = document.querySelector(".search-modal__container")
const searchBody = document.querySelector(".search-modal__body")
const closeBtn = document.querySelector(".search-modal__close")

search.addEventListener("click", function (event) {
  event.stopPropagation()
  searchModal.classList.add("open")
  setTimeout(() => {
    searchModalCont.classList.add("_active")
  }, 0)
  document.querySelector(".search-input").focus()
})

closeBtn.addEventListener("click", function () {
  searchModalCont.classList.remove("_active")
  setTimeout(() => {
    searchModal.classList.remove("open")
  }, 1000)
})

searchBody.addEventListener("click", function (event) {
  event.stopPropagation()
})
searchModal.addEventListener("click", function () {
  searchModalCont.classList.remove("_active")
  setTimeout(() => {
    searchModal.classList.remove("open")
  }, 500)
})
document.addEventListener("DOMContentLoaded", function () {
  const pathname = document.location.pathname
  const menuItems = document.querySelectorAll(
    ".header__bottom-burger__menu-item"
  )
  const menu = document.querySelector(".catalog-filter__menu")
  const btnFilter = document.querySelector(".catalog-filter__btn")
  const sidebar = document.querySelector(".sidebar")

  // menuItems.forEach(p => {
  //   if (p.pathname === pathname) {
  //     p.classList.add("active")
  //   }
  //   p.addEventListener("click", function () {
  //     btnBurger.style.cssText = "display: block"
  //     btnClose.style.cssText = "display: none"
  //     menu.classList.remove("open")
  //   })
  // })

  btnFilter.addEventListener("click", function () {
    menu.classList.toggle("open");
    sidebar.classList.toggle("open");
  })
})
// document.querySelector(".main").addEventListener("click", function () {
//   document
//     .querySelector(".header__bottom-burger__menu")
//     .classList.remove("open")
// })
ymaps.ready(init)

function init() {
  let map = new ymaps.Map("map", {
    center: [55.871011, 37.682916],
    zoom: 18,
    behaviors: ["drag"], //если передать пустой массив, то уберется зум при скролле страницы и перетаскивание карты, с drag перетаскивание осталось
    controls: ["zoomControl"] //убрать все элементы на карте кроме zoom
  })

  let placemark = new ymaps.Placemark(
    [55.871011, 37.682916],
    {
      hintContent: '<div class="map__hint">Шипиловская, д. 58, корп. 1</div>'
      // balloonContent: [
      //   '<div class="map__balloon">',
      //   '<img src="{{root}}assets/images/logo.png" alt="logo" />',
      //   "</div>"
      // ].join("")
    },
    {}
  )
  map.geoObjects.add(placemark)
  // map.controls.remove("geolocationControl") //удаляем геолокацию
  // map.controls.remove("searchControl") //удаляем поиск
  map.controls.remove("trafficControl") //удаляем контроль трафика
  map.controls.remove("typeSelector") //удаляем тип
  // map.controls.remove("fillscreenControl") //удаляем кнопку перехода в полноэкранный режим
  // map.controls.remove("zoomControl") //удаляем контроль зума
  // map.controls.remove("rulerControl") //удаляем контроль правил
  // map.controls.remove("scrollZoom") //удаляем скролл карты
}
document.addEventListener("DOMContentLoaded", function () {
  const formBtns = document.querySelectorAll(".form-btn")
  const modalClose = document.querySelector(".modal__close")
  const modal = document.querySelector(".modal")

  if (formBtns && !!modal && !!modalClose) {
    formBtns.forEach(formBtn => {
      formBtn.addEventListener("click", function () {
        modal.classList.add("open")
      })
    })

    modalClose.addEventListener("click", function () {
      modal.classList.remove("open")
    })
  }
})

document.addEventListener("DOMContentLoaded", function () {
  const inputs = document.querySelectorAll(".input")
  const phoneInput = document.getElementById("phone")
  const email = document.getElementById("email")
  const btn = document.querySelector(".form__btn")

  const maskOptions = {
    mask: "+{7}(000)000-00-00"
  }
  const mask = new IMask(phoneInput, maskOptions)
  function validateEmail(value) {
    const regEmail =
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return regEmail.test(value.toLowerCase())
  }

  const inputHandler = () => {
    const name = document.getElementById("name").value

    if (
      name.length > 3 &&
      mask.masked.isComplete &&
      validateEmail(email.value)
    ) {
      btn.disabled = false
      btn.classList.add("btn--active")
    } else {
      btn.classList.remove("btn--active")
    }
  }

  inputs.forEach(inp => inp.addEventListener("input", inputHandler))

  const messageModalClose = document.querySelector(".message-modal__close")
  const messageModal = document.querySelector(".message-modal")
  const modal = document.querySelector(".modal")
  btn.addEventListener("click", function (event) {
    event.preventDefault()
    messageModal.classList.add("open")
    inputs.forEach(element => {
      element.value = ""
    })
    modal.classList.remove("open")
  })

  messageModalClose.addEventListener("click", function () {
    messageModal.classList.remove("open")
  })
})
const productSwiper = new Swiper(".product-swiper", {
  slidesPerView: 1,
  loop: true,

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev"
  },
  thumbs: {
    swiper: {
      el: ".product-prev-swiper",
      slidesPerView: 4,
      spaceBetween: 5
    }
  }
})
const productTabs = document.querySelectorAll(".product__tabs-item")
const description = document.querySelector(".product__tabs__description")
const properties = document.querySelector(".product__tabs__properties")

productTabs.forEach(tab => {
  if (tab.classList.contains("active")) {
    value = tab.textContent
    changeTab(value)
  }
  tab.addEventListener("click", function (event) {
    if (!this.classList.contains("active")) {
      productTabs.forEach(tab => {
        tab.classList.remove("active")
      })
      this.classList.add("active")
      value = this.textContent
      changeTab(value)
    } else {
      this.classList.add("active")
    }
  })
})

function changeTab(value) {
  switch (value) {
    case "Описание":
      description.style.display = "block"
      properties.style.display = "none"
      break
    case "Характеристики":
      description.style.display = "none"
      properties.style.display = "block"
      break
    default:
      break
  }
}
const tabsItems = document.querySelectorAll(".catalog-filter__tabs-item")
tabsItems.forEach(tab => {
  tab.addEventListener("click", function (event) {
    if (!this.classList.contains("active")) {
      tabsItems.forEach(tab => {
        tab.classList.remove("active")
      })
      tab.classList.add("active")
    } else {
      tab.classList.add("active")
    }
  })
})

const parentCategories = document.querySelector(".catalog-filter__menu-item")
const categoryItems = parentCategories.querySelectorAll("li")
categoryItems.forEach(category => {
  category.addEventListener("click", function (event) {
    if (!this.classList.contains("active")) {
      categoryItems.forEach(category => {
        category.classList.remove("active")
      })
      category.classList.add("active")
    } else {
      category.classList.add("active")
    }
  })
})
const cards = [
  {
    id: 1,
    number: "536-3728-1",
    title: "Пояс монтажный УП А (ПП-1А) с капроновой лентой",
    photo: "./assets/images/product_1.png"
  },
  {
    id: 2,
    number: "536-3728-2",
    title: "Пояс монтажный УП А (ПП-1А) с капроновой лентой",
    photo: "./assets/images/product_2.png"
  },
  {
    id: 3,
    number: "536-3728-3",
    title: "Пояс монтажный УП I Г (ПП-1Г) с цепью",
    photo: "./assets/images/product_3.png"
  },
  {
    id: 4,
    number: "536-3728-4",
    title: "Строп А (лента) от 1,4 м с 2 карабинами",
    photo: "./assets/images/product_4.png"
  },
  {
    id: 5,
    number: "536-3728-5",
    title: "Строп В (канат) 20м с 1 карабином",
    photo: "./assets/images/product_5.png"
  },
  {
    id: 6,
    number: "536-3728-6",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  },
  {
    id: 7,
    number: "536-3728-7",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  },
  {
    id: 8,
    number: "536-3728-8",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  },
  {
    id: 9,
    number: "536-3728-9",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  },
  {
    id: 10,
    number: "536-3728-10",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  },
  {
    id: 11,
    number: "536-3728-11",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  },
  {
    id: 12,
    number: "536-3728-12",
    title: "Строп Г (цепь) 1,4 м с 2 карабинами",
    photo: "./assets/images/product_6.png"
  }
]

const getCardsWithPagination = () => {
  let currentPage = 1
  let numberOfCardsOnPage = 6

  const getCards = (arr, numberOfCards, page) => {
    const cardsEl = document.querySelector(".cards")
    cardsEl.innerHTML = ""
    const start = numberOfCards * (page - 1)
    const end = start + numberOfCards
    const currentCards = arr.slice(start, end)
    currentCards.forEach(card => {
      const cardEl = document.createElement("a")
      cardEl.classList.add("card")
      cardEl.href = "product.html"
      cardEl.innerHTML = `<span class="card__number">Артикул: ${card.number}</span>
        <h5 class="card__title">${card.title}</h5>
        <div class="card__img">
          <img src=${card.photo} alt="photo" />
        </div>
        <div class="card__btns">
          <div class="card__btns__counter">
            <span class="card__btns__counter-decrease">-</span>
            <p class="card__btns__counter-num active">0</p>
            <span class="card__btns__counter-increase">+</span>
          </div>
          <button class="card__btns__btn">
            <span class="card__btns__btn-text">В корзину</span>
            <svg
              width="46"
              height="42"
              viewBox="0 0 46 42"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M45.9726 9.97285L43.3748 23.4786C43.1605 24.5943 42.5402 25.6033 41.6219 26.3301C40.7037 27.0568 39.5456 27.4551 38.3495 27.4556H13.6491L14.586 32.3007H37.4765C38.4872 32.3007 39.4753 32.5849 40.3157 33.1173C41.1561 33.6497 41.8111 34.4064 42.1979 35.2917C42.5847 36.177 42.6859 37.1512 42.4887 38.0911C42.2915 39.0309 41.8048 39.8943 41.0901 40.5719C40.3754 41.2495 39.4648 41.7109 38.4735 41.8979C37.4822 42.0848 36.4546 41.9889 35.5208 41.6222C34.587 41.2554 33.7889 40.6344 33.2273 39.8377C32.6658 39.0409 32.3661 38.1041 32.3661 37.1459C32.3662 36.5952 32.4671 36.0488 32.6642 35.5308H20.1436C20.3407 36.0488 20.4415 36.5952 20.4417 37.1459C20.4433 37.9276 20.2453 38.698 19.8646 39.3915C19.484 40.0849 18.9321 40.6808 18.2559 41.1282C17.5797 41.5755 16.7994 41.8611 15.9816 41.9606C15.1638 42.0601 14.3327 41.9705 13.5593 41.6994C12.786 41.4284 12.0932 40.9839 11.5403 40.404C10.9874 39.8241 10.5906 39.126 10.384 38.3692C10.1773 37.6125 10.1669 36.8195 10.3535 36.0581C10.5402 35.2966 10.9184 34.5893 11.4559 33.9965L5.55759 3.23007H1.70348C1.25169 3.23007 0.8184 3.05992 0.498937 2.75704C0.179473 2.45416 0 2.04337 0 1.61504C0 1.1867 0.179473 0.775912 0.498937 0.473033C0.8184 0.170155 1.25169 0 1.70348 0H5.55759C6.35277 0.00164915 7.1223 0.266953 7.73286 0.74995C8.34341 1.23295 8.75654 1.90321 8.90066 2.64462L9.94404 8.07519H44.2904C44.5391 8.07567 44.7846 8.12844 45.0092 8.22967C45.2338 8.3309 45.4319 8.47809 45.5893 8.66064C45.754 8.83745 45.8741 9.04749 45.9405 9.27484C46.0069 9.50219 46.0179 9.74089 45.9726 9.97285Z"
              />
            </svg>
          </button>
        </div>`
      cardsEl.appendChild(cardEl)
    })
  }

  const getPagination = (arr, numberOfCards) => {
    const paginationEl = document.querySelector(".pagination")
    const pagesCount = Math.ceil(arr.length / numberOfCards)
    const ulEl = document.createElement("ul")
    ulEl.classList.add("pagination__list")

    for (let i = 0; i < pagesCount; i++) {
      const paginationItemEl = getPaginationBtn(i + 1)
      ulEl.appendChild(paginationItemEl)
    }
    paginationEl.appendChild(ulEl)
  }

  const getPaginationBtn = pageNumber => {
    const liEl = document.createElement("li")
    liEl.classList.add("pagination__list-item")
    const spanEl = document.createElement("span")
    spanEl.innerText = pageNumber
    liEl.appendChild(spanEl)
    if (currentPage === pageNumber)
      liEl.classList.add("pagination__list-item--active")
    liEl.addEventListener("click", () => {
      currentPage = pageNumber
      const currentActiveEl = document.querySelector(
        "li.pagination__list-item--active"
      )
      currentActiveEl.classList.remove("pagination__list-item--active")
      liEl.classList.add("pagination__list-item--active")
      getCards(cards, numberOfCardsOnPage, currentPage)
    })
    return liEl
  }

  getCards(cards, numberOfCardsOnPage, currentPage)
  getPagination(cards, numberOfCardsOnPage)
}
getCardsWithPagination()
const accBtns = document.querySelectorAll(".accordion-item__btn")

if (accBtns) {
  accBtns.forEach(btn => {
    btn.addEventListener("click", function () {
      this.classList.toggle("active")
      const btnRight = this.querySelector(".accordion-item__btn-icon-right")
      const btnUp = this.querySelector(".accordion-item__btn-icon-up")
      if (this.classList && this.classList.contains("active")) {
        btnRight.style.cssText = "display: none"
        btnUp.style.cssText = "display: block"
      } else {
        btnRight.style.cssText = "display: block"
        btnUp.style.cssText = "display: none"
      }

      const list = this.nextElementSibling
      if (list.style.maxHeight) {
        list.style.maxHeight = null
      } else {
        list.style.maxHeight = list.scrollHeight + "px"
      }
    })
  })
}

