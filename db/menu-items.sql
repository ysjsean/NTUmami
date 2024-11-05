-- Foods data for each stall with filled is_halal, is_vegetarian, is_in_stock, and created_by fields

INSERT INTO foods (id, stall_id, name, price, description, image_url, is_halal, is_vegetarian, is_in_stock, created_by, updated_by) VALUES
-- Noodle Stall (stall_id 5)
(1, 5, 'Seafood Hor Fun', 5.50, 'Flat rice noodles with fresh seafood in a savory sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(2, 5, 'Beef Noodle Soup', 6.00, 'Tender beef slices with rice noodles in a flavorful broth', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(3, 5, 'Tom Yum Noodles', 5.00, 'Spicy and tangy Thai-style noodles with seafood', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Kiso Japanese Cuisine (stall_id 6)
(4, 6, 'Ramen', 8.00, 'Japanese noodle soup with pork and flavorful broth', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(6, 6, 'Chicken Katsu', 7.50, 'Breaded fried chicken served with rice', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(7, 6, 'Tempura Udon', 7.50, 'Thick wheat noodles in broth topped with crispy tempura', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Si Chuan Mei Shi (stall_id 7)
(8, 7, 'Mapo Tofu', 5.00, 'Spicy Sichuan dish with tofu and minced meat', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(9, 7, 'Kung Pao Chicken', 6.00, 'Stir-fried chicken with peanuts in a spicy sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Western (stall_id 10)
(12, 10, 'Grilled Chicken', 7.00, 'Juicy grilled chicken served with mashed potatoes', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(13, 10, 'Fish and Chips', 8.50, 'Classic battered fish served with fries', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Taiwan Cuisine (stall_id 11)
(16, 11, 'Braised Pork Rice', 5.00, 'Rice topped with braised pork in soy sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(17, 11, 'Taiwanese Popcorn Chicken', 4.50, 'Crispy fried chicken bites with spices', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Braised Duck Rice (stall_id 12)
(19, 12, 'Duck Rice', 5.50, 'Braised duck served with fragrant rice', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(20, 12, 'Duck Noodles', 6.00, 'Noodles with braised duck in rich broth', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(21, 12, 'Duck Porridge', 4.50, 'Rice porridge with tender braised duck', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Bai Li Xiang Economic Bee Hoon (stall_id 13)
(22, 13, 'Fried Bee Hoon', 3.00, 'Stir-fried rice vermicelli with vegetables', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(23, 13, 'Fried Kway Teow', 3.50, 'Stir-fried flat noodles with egg and veggies', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(24, 13, 'Nasi Lemak', 4.00, 'Rice with coconut milk, anchovies, and sambal', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Ananda’s Restaurant (stall_id 14)
(25, 14, 'Butter Chicken', 7.50, 'Indian dish with creamy tomato-based sauce', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(26, 14, 'Paneer Tikka', 6.00, 'Grilled paneer cubes marinated in spices', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- One Chicken (stall_id 15)
(28, 15, 'Roasted Chicken Rice', 5.00, 'Roasted chicken served with fragrant rice', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(29, 15, 'Hainanese Chicken Rice', 5.00, 'Poached chicken with rice cooked in chicken broth', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(30, 15, 'Chicken Porridge', 4.50, 'Rice porridge with shredded chicken', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Fortune 16 Drinks (stall_id 16)
(31, 16, 'Lemon Tea', 1.50, 'Refreshing iced lemon tea', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(32, 16, 'Bandung', 1.80, 'Rose syrup with milk', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(33, 16, 'Iced Milo', 2.00, 'Chocolate malt drink served cold', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Menya Takashi (stall_id 19)
(40, 19, 'Shoyu Ramen', 8.50, 'Soy sauce flavored ramen with pork', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(41, 19, 'Tonkotsu Ramen', 9.00, 'Rich pork broth ramen with sliced pork', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Fish Soup Ban Mian (stall_id 20)
(43, 20, 'Ban Mian', 5.50, 'Handmade noodles in fish broth', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(45, 20, 'Fried Fish Soup', 6.50, 'Soup with crispy fried fish pieces', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Asia Farm Drinks (stall_id 21)
(46, 21, 'Soy Milk', 1.50, 'Chilled soy milk drink', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(47, 21, 'Grass Jelly Drink', 1.80, 'Refreshing drink with grass jelly', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(48, 21, 'Coconut Water', 2.00, 'Fresh coconut water served chilled', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Wuhan Delicacies (stall_id 22)
(49, 22, 'Spicy Hot Dry Noodles', 5.00, 'Popular Wuhan noodles with a spicy sesame sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Xi An Cuisine (stall_id 23)
(52, 23, 'Biang Biang Noodles', 6.00, 'Thick, hand-pulled noodles with spicy sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(54, 23, 'Roujiamo', 4.50, 'Chinese-style sandwich with spiced meat filling', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Mini Wok (stall_id 24)
(55, 24, 'Stir-fried Mixed Vegetables', 4.00, 'Fresh vegetables stir-fried with light seasoning', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(56, 24, 'Sweet and Sour Pork', 5.50, 'Crispy pork pieces in a tangy sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(57, 24, 'Fried Rice with Chicken', 5.00, 'Fried rice with chunks of chicken and vegetables', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Western Cuisine (stall_id 26)
(61, 26, 'Chicken Chop', 6.50, 'Grilled chicken with mushroom sauce and sides', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(62, 26, 'Beef Steak', 8.50, 'Juicy beef steak served with mashed potatoes', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(63, 26, 'Spaghetti Bolognese', 6.00, 'Spaghetti with a rich meat sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Korean Delights (stall_id 27)
(64, 27, 'Japchae', 5.50, 'Sweet potato noodles stir-fried with vegetables', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Roasted Delights (stall_id 28)
(67, 28, 'Char Siew Rice', 5.00, 'Sweet barbecued pork served with rice', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(68, 28, 'Roast Duck Rice', 5.50, 'Tender roast duck served with rice', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Taiwan Food (stall_id 29)
(70, 29, 'Gua Bao', 4.50, 'Steamed bun with braised pork and pickled veggies', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),
(71, 29, 'Beef Noodle Soup', 6.00, 'Noodles in a rich beef broth', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Vegetarian (stall_id 30)
(73, 30, 'Vegetable Fried Rice', 4.50, 'Fried rice with mixed vegetables', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(74, 30, 'Stir-fried Broccoli', 4.00, 'Fresh broccoli stir-fried with garlic', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(75, 30, 'Tofu Stir Fry', 4.50, 'Tofu stir-fried with bell peppers and mushrooms', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Koufu Drinks (stall_id 31)
(76, 31, 'Iced Lemon Tea', 1.50, 'Refreshing iced tea with a hint of lemon', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(77, 31, 'Milo Dinosaur', 2.00, 'Chocolate malt drink topped with Milo powder', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(78, 31, 'Kopi', 1.20, 'Traditional Singaporean coffee', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Fruit & Juices (stall_id 32)
(79, 32, 'Fresh Orange Juice', 2.50, 'Freshly squeezed orange juice', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(80, 32, 'Watermelon Juice', 2.00, 'Fresh watermelon juice served chilled', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Malay Food (stall_id 33)
(82, 33, 'Nasi Lemak', 4.50, 'Coconut rice with anchovies, peanuts, and sambal', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(83, 33, 'Mee Rebus', 4.00, 'Yellow noodles in a spicy, thick gravy', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Chicken Rice (stall_id 40)
(100, 40, 'Hainanese Chicken Rice', 5.00, 'Poached chicken with rice cooked in chicken broth', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(101, 40, 'Roasted Chicken Rice', 5.00, 'Roasted chicken served with fragrant rice', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Japanese & Korean (stall_id 41)
(104, 41, 'Bulgogi Rice', 7.00, 'Grilled marinated beef served over rice', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(105, 41, 'Kimchi Fried Rice', 6.00, 'Fried rice with kimchi and egg', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Vegetarian (stall_id 42)
(106, 42, 'Vegetable Briyani', 5.50, 'Basmati rice with a mix of vegetables and spices', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(107, 42, 'Paneer Butter Masala', 6.50, 'Cottage cheese in a creamy tomato-based sauce', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Indian Food (stall_id 44)
(109, 44, 'Butter Chicken', 7.00, 'Tender chicken in a creamy tomato-based sauce', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(110, 44, 'Lamb Curry', 8.00, 'Slow-cooked lamb in a rich, spicy curry', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Fish Soup Ban Mian (stall_id 49)
(125, 49, 'Fried Fish Soup', 5.50, 'Clear soup with crispy fried fish', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(126, 49, 'Ban Mian', 5.50, 'Handmade noodles in fish broth with vegetables', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Nasi Ayam (stall_id 51)
(130, 51, 'Nasi Ayam', 6.00, 'Fragrant rice served with fried or roasted chicken', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Japanese Cuisine (stall_id 52)
(133, 52, 'Chicken Katsu Don', 7.00, 'Rice bowl topped with breaded chicken cutlet', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(135, 52, 'Tempura Udon', 7.50, 'Udon noodles served with crispy tempura', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Sandwiches & Salad Bar (stall_id 53)
(136, 53, 'Caesar Salad', 5.00, 'Classic Caesar salad with romaine and Parmesan', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(137, 53, 'Chicken Avocado Sandwich', 6.50, 'Grilled chicken with avocado in a fresh sandwich', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Paofan (stall_id 54)
(139, 54, 'Seafood Paofan', 8.00, 'Rice in a rich seafood broth with shrimp and fish', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(140, 54, 'Chicken Paofan', 7.00, 'Rice soaked in chicken broth with chicken slices', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(141, 54, 'Vegetable Paofan', 6.00, 'Rice in a light vegetable broth with greens', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Korean (stall_id 57)
(149, 57, 'Bibimbap', 7.50, 'Rice bowl with assorted vegetables, egg, and meat', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(151, 57, 'Kimchi Stew', 6.00, 'Spicy stew with kimchi and tofu', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Thai Cuisine (stall_id 58)
(152, 58, 'Pad Thai', 6.00, 'Stir-fried rice noodles with shrimp, tofu, and peanuts', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(153, 58, 'Green Curry Chicken', 6.50, 'Thai green curry with chicken and vegetables', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Xiao Long Bao (stall_id 59)
(157, 59, 'Chicken Xiao Long Bao', 5.50, 'Steamed dumplings filled with chicken and soup', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(158, 59, 'Vegetable Dumplings', 5.00, 'Steamed dumplings filled with vegetables', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Mini Wok (stall_id 62)
(218, 62, 'Stir-fried Mixed Vegetables', 4.00, 'Assorted fresh vegetables stir-fried in a savory sauce', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(219, 62, 'Sweet and Sour Pork', 5.50, 'Crispy pork in a sweet and sour sauce', NULL, 0, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Curry Rice (stall_id 63)
(222, 63, 'Chicken Curry Rice', 5.50, 'Steamed rice topped with flavorful chicken curry', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(223, 63, 'Fish Curry Rice', 6.00, 'Rice served with spicy fish curry', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Nasi Lemak (stall_id 74)
(203, 74, 'Fried Chicken Nasi Lemak', 5.50, 'Nasi lemak with crispy fried chicken', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(204, 74, 'Egg Nasi Lemak', 4.00, 'Nasi lemak served with fried egg and sambal', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Thai Cuisine (stall_id 75)
(205, 75, 'Pad Thai', 6.00, 'Stir-fried rice noodles with shrimp, tofu, and peanuts', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(207, 75, 'Mango Sticky Rice', 5.00, 'Sweet sticky rice with mango slices', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),

-- Porridge (stall_id 78)
(211, 78, 'Century Egg Porridge', 4.50, 'Smooth porridge with century egg and green onions', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(212, 78, 'Chicken Porridge', 4.00, 'Warm porridge with shredded chicken', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),
(213, 78, 'Fish Porridge', 4.50, 'Light porridge with fresh fish slices', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL),

-- Indian (stall_id 79)
(214, 79, 'Masala Dosa', 4.50, 'Crispy rice crepe filled with spiced potato', NULL, 1, 1, 1, CURRENT_TIMESTAMP(), NULL),
(215, 79, 'Chicken Biryani', 7.00, 'Aromatic rice cooked with spices and chicken', NULL, 1, 0, 1, CURRENT_TIMESTAMP(), NULL);