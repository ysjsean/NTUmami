# NTUmami

IE4727 Web Project - Web portal for online food ordering in NTU

**Language Used:** PHP, HTML5, CSS3, Javascript, SQL
**Database:**: Relational Database
**Restrictions:** Unable to use JSON, AJAX, Templates and Libraries etc.

## **Directory Structure:**

```
NTUmami
|   README.md
|   index.php
└───assets
|   └───css
|   └───images
|   └───js
└───controllers
|   |   handler.php
|   |   ...
└───db
|   |   ntumami.sql (dump)
└───includes
|   |   cart_number.php
|   |   db_connect.php
|   |   footer.php
|   |   header.php
└───pages
|   |   login.php
|   |   cart.php
|   |   ...
```

## **Sitemap:**

1. User
   - Home
   - Menu
     View every canteens and stalls according to their opening hours
     Display "Out of stock", "Closed" status
     Filter by Location, Cuisine and Dietary
     Add to cart
   - Locations
   - About Us
   - My Orders
     View Ongoing Orders and Order History
     Change status of each food item (Collected) to complete
   - Cart Icon
     - View, Update and Delete cart items
     - Checkout to enter payment details and see summary
   - Login / Logout / View Profile
2. Admin Dashboard
   - Canteens
     To Add, View, Update and Delete
   - Vendors
     To Add, View, Update and Delete
   - Stalls
     To Add, View, Update and Delete
3. Vendor Dashboard (Individual stall has 1 vendor each)
   - Stalls
     Update stall status (Open, Closed)
   - Foods
     To Add, View, Update and Delete
   - Orders
     View and change status of incoming orders
     Status (Pending, Preparing, Ready for Pickup)
   - Order Summary
     Display summary of each food item and revenue earned.
     Display Overall Revenue and Best-Selling Food
   - Profile
     View and update profile
     Change password

## **Database Tables:**

1. users
2. user_profiles
3. vendors
4. orders
5. order_items
6. foods
7. carts
8. cart_items
9. canteens
10. canteen_hours
11. feedback
12. payments
13. saved_payment_methods
14. stalls

## **Our Team:**

Sean Young Song Jie
Bryan Koh Zhe Qi

**Contributions**
| Sean | Bryan |
| :------------: | :-------------: |
| Header | Index |
| Footer | About Us |
| Login / Sign up | Location |
| Cart | Menu |
| Checkout / Payment | My Orders |
| Admin Dashboard | My Profile |
| Vendor Dashboard | |
| Database Design | |
