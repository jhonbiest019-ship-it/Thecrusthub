const PRODUCTS = [
  {
    id: 1,
    category: "Cakes",
    title: "Chocolate Fudge Cake",
    description: "Rich homemade chocolate sponge layered with fudge.",
    price: 3200,
    image: "https://images.pexels.com/photos/291528/pexels-photo-291528.jpeg?auto=compress&cs=tinysrgb&w=1200"
  },
  {
    id: 2,
    category: "Cakes",
    title: "Red Velvet Cake",
    description: "Soft red velvet cake with creamy frosting.",
    price: 3000,
    image: "Web Image/Red%20Velvet%20Cake.jfif"
  },
  {
    id: 3,
    category: "Cakes",
    title: "Coffee Walnut Cake",
    description: "Homemade coffee sponge cake with walnut crunch.",
    price: 3100,
    image: "Web Image/Coffee%20Walnut%20Cake.jfif"
  },
  {
    id: 4,
    category: "Cakes",
    title: "Pineapple Cream Cake",
    description: "Fresh cream pineapple cake with light sponge layers.",
    price: 2900,
    image: "Web Image/Pineapple%20Cream%20Cake.jfif"
  },
  {
    id: 5,
    category: "Cakes",
    title: "Black Forest Cake",
    description: "Classic black forest cake with cherry and cream.",
    price: 3300,
    image: "Web Image/Black%20Forest%20Cake.jfif"
  },
  {
    id: 6,
    category: "Cookies",
    title: "Butter Almond Cookies",
    description: "Crisp butter cookies with roasted almond flavor.",
    price: 1100,
    image: "Web Image/Butter%20Almond%20Cookies.jfif"
  },
  {
    id: 7,
    category: "Cookies",
    title: "Nankhatai",
    description: "Traditional homemade nankhatai with cardamom aroma.",
    price: 780,
    image: "Web Image/Nankhatai.jfif"
  },
  {
    id: 8,
    category: "Cookies",
    title: "Coconut Macaroons",
    description: "Soft coconut macaroons with golden baked edges.",
    price: 820,
    image: "Web Image/Coconut%20Macaroons.jfif"
  },
  {
    id: 9,
    category: "Cookies",
    title: "Chocolate Chip Cookies",
    description: "Chunky homemade cookies with dark chocolate chips.",
    price: 1200,
    image: "Web Image/Chocolate%20Chip%20Cookies.jfif"
  },
  {
    id: 10,
    category: "Cookies",
    title: "Zeera Biscuit",
    description: "Savory cumin biscuits baked in homemade style.",
    price: 650,
    image: "Web Image/Zeera%20Biscuit.jfif"
  },
  {
    id: 11,
    category: "Pastries",
    title: "Vanilla Berry Pastry",
    description: "Light vanilla cream pastry topped with fresh berries.",
    price: 650,
    image: "Web Image/Vanilla%20Berry%20Pastry.jfif"
  },
  {
    id: 12,
    category: "Pastries",
    title: "Chocolate Mousse Pastry",
    description: "Smooth chocolate mousse pastry with soft sponge base.",
    price: 700,
    image: "Web Image/Chocolate%20Mousse%20Pastry.jfif"
  },
  {
    id: 13,
    category: "Pastries",
    title: "Pineapple Pastry",
    description: "Fresh cream pineapple pastry in bakery style.",
    price: 620,
    image: "Web Image/Pineapple%20Pastry.jfif"
  },
  {
    id: 14,
    category: "Pastries",
    title: "Coffee Caramel Pastry",
    description: "Coffee-flavored pastry with silky caramel topping.",
    price: 720,
    image: "Web Image/Coffee%20Caramel%20Pastry.jfif"
  },
  {
    id: 15,
    category: "Pastries",
    title: "Oreo Cream Pastry",
    description: "Creamy pastry layered with crushed Oreo biscuits.",
    price: 740,
    image: "Web Image/Oreo%20Cream%20Pastry.jfif"
  },
  {
    id: 16,
    category: "Desserts",
    title: "Gajar Halwa",
    description: "Slow-cooked homemade carrot halwa with khoya and nuts.",
    price: 950,
    image: "Web Image/Gajar%20Halwa.jfif"
  },
  {
    id: 17,
    category: "Desserts",
    title: "Shahi Kheer",
    description: "Creamy rice kheer infused with saffron and cardamom.",
    price: 700,
    image: "Web Image/Shahi%20Kheer.jfif"
  },
  {
    id: 18,
    category: "Desserts",
    title: "Lab-e-Shireen",
    description: "Festive creamy dessert with fruit and jelly bits.",
    price: 900,
    image: "Web Image/Lab-e-Shireen.jfif"
  },
  {
    id: 19,
    category: "Desserts",
    title: "Rabri Falooda",
    description: "Rabri falooda topped with nuts and rose syrup.",
    price: 880,
    image: "Web Image/Rabri%20Falooda.jfif"
  },
  {
    id: 20,
    category: "Desserts",
    title: "Zarda Rice Delight",
    description: "Sweet saffron rice dessert with dry fruits.",
    price: 860,
    image: "Web Image/Zarda%20Rice%20Delight.jfif"
  },
  {
    id: 21,
    category: "Snacks",
    title: "Chicken Patties",
    description: "Flaky puff patties with spicy homemade chicken filling.",
    price: 240,
    image: "Web Image/Chicken%20Patties.jfif"
  },
  {
    id: 22,
    category: "Snacks",
    title: "Aloo Patties",
    description: "Homemade puff patties stuffed with masala potatoes.",
    price: 200,
    image: "Web Image/Aloo%20Patties.jfif"
  },
  {
    id: 23,
    category: "Snacks",
    title: "Mini Pizza Buns",
    description: "Soft buns topped with homemade pizza filling.",
    price: 280,
    image: "Web Image/Mini%20Pizza%20Buns.jfif"
  },
  {
    id: 24,
    category: "Snacks",
    title: "Chicken Bread",
    description: "Baked bread roll stuffed with creamy chicken filling.",
    price: 520,
    image: "Web Image/Chicken%20Bread.jfif"
  },
  {
    id: 25,
    category: "Snacks",
    title: "Cheese Samosa",
    description: "Crispy baked samosa filled with cheese and herbs.",
    price: 260,
    image: "Web Image/Cheese%20Samosa.jfif"
  }
];

const WHATSAPP_NUMBER = "923331110001";
const LEAD_HOURS = 24;
const CURRENCY = "PKR";
const FALLBACK_IMAGE = "https://images.pexels.com/photos/291528/pexels-photo-291528.jpeg?auto=compress&cs=tinysrgb&w=1200";

const state = {
  activeCategory: "All",
  cart: []
};

const productGrid = document.getElementById("productGrid");
const categoryFilters = document.getElementById("categoryFilters");
const cartItems = document.getElementById("cartItems");
const cartTotal = document.getElementById("cartTotal");
const checkoutForm = document.getElementById("checkoutForm");
const checkoutError = document.getElementById("checkoutError");
const deliveryTime = document.getElementById("deliveryTime");
const quickViewModal = document.getElementById("quickViewModal");
const modalContent = document.getElementById("modalContent");

function money(value) {
  return `${CURRENCY} ${Number(value).toFixed(2)}`;
}

function categories() {
  return ["All", ...new Set(PRODUCTS.map((p) => p.category))];
}

function renderCategories() {
  categoryFilters.innerHTML = categories()
    .map((cat) => `<button class="chip ${cat === state.activeCategory ? "active" : ""}" data-cat="${cat}" type="button">${cat}</button>`)
    .join("");
}

function filteredProducts() {
  if (state.activeCategory === "All") return PRODUCTS;
  return PRODUCTS.filter((p) => p.category === state.activeCategory);
}

function renderProducts() {
  const cards = filteredProducts()
    .map(
      (p) => `
      <article class="product-card glass">
        <img src="${p.image}" alt="${p.title}" onerror="this.onerror=null;this.src='${FALLBACK_IMAGE}'">
        <h3>${p.title}</h3>
        <p>${p.description}</p>
        <div class="price">${money(p.price)}</div>
        <div class="btn-row">
          <button class="btn-alt quick-view" data-id="${p.id}" type="button">Quick View</button>
          <button class="add-cart" data-id="${p.id}" type="button">Add to Cart</button>
        </div>
      </article>
    `
    )
    .join("");
  productGrid.innerHTML = cards || `<div class="glass product-card">No products found.</div>`;
}

function renderCart() {
  if (!state.cart.length) {
    cartItems.innerHTML = "<p>Cart is empty.</p>";
    cartTotal.textContent = money(0);
    return;
  }

  cartItems.innerHTML = state.cart
    .map((item) => `<div class="cart-item"><span>${item.title} x ${item.qty}</span><span>${money(item.qty * item.price)}</span></div>`)
    .join("");

  const total = state.cart.reduce((sum, item) => sum + item.qty * item.price, 0);
  cartTotal.textContent = money(total);
}

function addToCart(id) {
  const product = PRODUCTS.find((p) => p.id === id);
  if (!product) return;

  const existing = state.cart.find((item) => item.id === id);
  if (existing) existing.qty += 1;
  else state.cart.push({ id: product.id, title: product.title, price: product.price, qty: 1, image: product.image });
  renderCart();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function openQuickView(id) {
  const product = PRODUCTS.find((p) => p.id === id);
  if (!product) return;

  modalContent.innerHTML = `
    <img src="${product.image}" alt="${product.title}" onerror="this.onerror=null;this.src='${FALLBACK_IMAGE}'">
    <div class="modal-info">
      <h3>${product.title}</h3>
      <p>${product.description}</p>
      <p class="price">${money(product.price)}</p>
      <button id="modalAddToCart" type="button">Add to Cart</button>
    </div>
  `;

  quickViewModal.classList.add("open");
  document.getElementById("modalAddToCart").addEventListener("click", () => {
    addToCart(id);
    quickViewModal.classList.remove("open");
  });
}

function closeModal() {
  quickViewModal.classList.remove("open");
}

function validateLeadTime(dateTimeValue) {
  const selected = new Date(dateTimeValue.replace(" ", "T")).getTime();
  const min = Date.now() + LEAD_HOURS * 60 * 60 * 1000;
  return Number.isFinite(selected) && selected >= min;
}

function placeWhatsAppOrder(customerName, phone, address, delivery) {
  const items = state.cart.map((item) => `${item.title} x ${item.qty}`).join(", ");
  const total = state.cart.reduce((sum, item) => sum + item.qty * item.price, 0);
  const imageRef = state.cart[0]?.image || "https://en.wikipedia.org/wiki/Main_product";

  const msg = [
    "*The Crust Hub Order*",
    "------------------",
    `Customer: ${customerName}`,
    `Phone: ${phone}`,
    `Address: ${address}`,
    `Items: ${items}`,
    `Total: ${money(total)}`,
    `Delivery: ${delivery}`,
    `Image Reference: ${imageRef}`
  ].join("\n");

  const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`;
  window.open(url, "_blank");
}

function bindEvents() {
  categoryFilters.addEventListener("click", (e) => {
    const btn = e.target.closest(".chip");
    if (!btn) return;
    state.activeCategory = btn.dataset.cat;
    renderCategories();
    renderProducts();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  productGrid.addEventListener("click", (e) => {
    const addBtn = e.target.closest(".add-cart");
    const quickBtn = e.target.closest(".quick-view");
    if (addBtn) addToCart(Number(addBtn.dataset.id));
    if (quickBtn) openQuickView(Number(quickBtn.dataset.id));
  });

  document.getElementById("closeModal").addEventListener("click", closeModal);
  document.querySelector(".modal-backdrop").addEventListener("click", closeModal);

  checkoutForm.addEventListener("submit", (e) => {
    e.preventDefault();
    checkoutError.textContent = "";

    const customerName = document.getElementById("customerName").value.trim();
    const phone = document.getElementById("customerPhone").value.trim();
    const address = document.getElementById("customerAddress").value.trim();
    const delivery = deliveryTime.value.trim();

    if (!customerName || customerName.length < 2) {
      checkoutError.textContent = "Please enter a valid customer name.";
      return;
    }
    if (!phone || phone.length < 10) {
      checkoutError.textContent = "Please enter a valid mobile number.";
      return;
    }
    if (!address || address.length < 5) {
      checkoutError.textContent = "Please enter a complete delivery address.";
      return;
    }
    if (!state.cart.length) {
      checkoutError.textContent = "Your cart is empty.";
      return;
    }
    if (!delivery) {
      checkoutError.textContent = "Please select delivery date/time.";
      return;
    }
    if (!validateLeadTime(delivery)) {
      checkoutError.textContent = "Order must be placed 24 hours in advance.";
      return;
    }

    placeWhatsAppOrder(customerName, phone, address, delivery);
    state.cart = [];
    renderCart();
    checkoutForm.reset();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

function init() {
  flatpickr("#deliveryTime", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    minDate: new Date(Date.now() + LEAD_HOURS * 60 * 60 * 1000)
  });
  renderCategories();
  renderProducts();
  renderCart();
  bindEvents();
}

init();
