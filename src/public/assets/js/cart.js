/**
 * cart.js — Giỏ hàng dùng chung (localStorage)
 * Được dùng bởi: index.html, product_details.html, shopping_cart.html
 */

const CART_KEY = 'aurrelia_cart';

const Cart = {
  getAll() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch { return []; }
  },

  save(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
    window.dispatchEvent(new Event('cart-updated'));
  },

  add(product) {
    const items = this.getAll();
    const existing = items.find(i => i.id === product.id && i.metal === product.metal);
    if (existing) {
      existing.quantity += product.quantity || 1;
    } else {
      items.push({ ...product, quantity: product.quantity || 1 });
    }
    this.save(items);
    return items;
  },

  remove(id, metal) {
    const items = this.getAll().filter(i => !(i.id === id && i.metal === metal));
    this.save(items);
    return items;
  },

  updateQuantity(id, metal, delta) {
    const items = this.getAll();
    const item = items.find(i => i.id === id && i.metal === metal);
    if (!item) return items;
    item.quantity += delta;
    if (item.quantity <= 0) return this.remove(id, metal);
    this.save(items);
    return items;
  },

  setQuantity(id, metal, qty) {
    const items = this.getAll();
    const item = items.find(i => i.id === id && i.metal === metal);
    if (!item) return items;
    if (qty <= 0) return this.remove(id, metal);
    item.quantity = qty;
    this.save(items);
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
  }
};

function formatVND(amount) {
  return amount.toLocaleString('vi-VN') + '₫';
}