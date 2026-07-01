const CART_KEY = "aurrelia_cart";
const IS_LOGGED_IN = window.USER_LOGGED_IN || window.IS_LOGGED_IN || false;

async function syncDB(action, data = {}) {
  if (!IS_LOGGED_IN) return;
  try {
    const form = new FormData();
    form.append("action", action);
    Object.entries(data).forEach(([k, v]) => {
      if (v != null) form.append(k, v);
    });
    await fetch("/index.php?page=cart&action=" + action, {
      method: "POST",
      body: form,
    });
  } catch (e) {
    console.warn("Cart sync lỗi:", e);
  }
}

const Cart = {
  getAll() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch {
      return [];
    }
  },
  save(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
    window.dispatchEvent(new Event("cart-updated"));
  },
  add(product) {
    const items = this.getAll();
    const existing = items.find(
      (i) => i.id === product.id && i.metal === product.metal,
    );
    if (existing) {
      existing.quantity += product.quantity || 1;
    } else {
      items.push({ ...product, quantity: product.quantity || 1 });
    }
    this.save(items);
    syncDB("add", {
      product_id: product.id,
      quantity: product.quantity || 1,
      selected_material: product.metal !== "default" ? product.metal : null,
    });
    return items;
  },
  remove(id, metal) {
    const items = this.getAll().filter(
      (i) => !(i.id === id && i.metal === metal),
    );
    this.save(items);
    syncDB("remove", { product_id: id });
    return items;
  },
  updateQuantity(id, metal, delta) {
    const items = this.getAll();
    const item = items.find((i) => i.id === id && i.metal === metal);
    if (!item) return items;
    item.quantity += delta;
    if (item.quantity <= 0) return this.remove(id, metal);
    this.save(items);
    syncDB("update", { product_id: id, quantity: item.quantity });
    return items;
  },
  setQuantity(id, metal, qty) {
    const items = this.getAll();
    const item = items.find((i) => i.id === id && i.metal === metal);
    if (!item) return items;
    if (qty <= 0) return this.remove(id, metal);
    item.quantity = qty;
    this.save(items);
    syncDB("update", { product_id: id, quantity: qty });
    return items;
  },
  getTotalQty() {
    return this.getAll().reduce((s, i) => s + i.quantity, 0);
  },
  getTotalPrice() {
    return this.getAll().reduce((s, i) => s + i.price * i.quantity, 0);
  },
  clear() {
    this.save([]);
    syncDB("clear");
  },
};

function formatVND(amount) {
  return amount.toLocaleString("vi-VN") + "₫";
}
