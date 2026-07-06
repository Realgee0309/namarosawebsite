-- ============================================================
-- Namarosa Hardware - Database Schema
-- www.namarosahardware.co.ke
-- ============================================================

CREATE DATABASE IF NOT EXISTS namarosa_hardware CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE namarosa_hardware;

-- Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(100) DEFAULT 'fa-box',
    description TEXT,
    sort_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    specifications TEXT,
    price DECIMAL(10,2) DEFAULT NULL,
    unit VARCHAR(50) DEFAULT 'piece',
    image_url VARCHAR(255) DEFAULT NULL,
    in_stock TINYINT(1) DEFAULT 1,
    featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Quote Requests (RFQ)
CREATE TABLE quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(200) NOT NULL,
    company_name VARCHAR(200) DEFAULT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(200) DEFAULT NULL,
    delivery_location TEXT NOT NULL,
    expected_date DATE DEFAULT NULL,
    materials_text TEXT NOT NULL,
    status ENUM('new','read','responded','closed') DEFAULT 'new',
    admin_notes TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Quote Line Items (from product catalog add-to-quote)
CREATE TABLE quote_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1,
    unit VARCHAR(50) DEFAULT 'piece',
    notes VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Admin Users
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(200) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(200) DEFAULT NULL,
    last_login TIMESTAMP NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default admin (password: Admin@2024 — CHANGE THIS IMMEDIATELY)
INSERT INTO admins (username, email, password_hash, full_name) VALUES
('admin', 'emmanueljuma1972@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emmanuel Juma');

-- Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Namarosa Hardware'),
('site_tagline', 'Your Partner in Building & Progress'),
('phone', '+254 710 774 174'),
('whatsapp', '254710774174'),
('email', 'emmanueljuma1972@gmail.com'),
('address', 'Main Street, Bungoma, Bungoma County, Kenya'),
('hours', 'Mon–Sat: 7:30 AM – 6:00 PM'),
('google_maps_embed', 'https://maps.google.com/maps?q=Namarosa+Hardware+Bungoma&output=embed'),
('director_name', 'Emmanuel Juma'),
('director_title', 'Director'),
('facebook_url', ''),
('twitter_url', ''),
('instagram_url', '');

-- Categories
INSERT INTO categories (name, slug, icon, description, sort_order) VALUES
('Building Materials', 'building-materials', 'fa-building', 'Cement, sand, ballast, blocks, timber, and all foundation needs', 1),
('Structural Steel', 'structural-steel', 'fa-industry', 'Steel bars, pipes, angles, channels, and structural sections', 2),
('Hand & Power Tools', 'tools', 'fa-tools', 'Quality hand tools, power tools, and accessories for every trade', 3),
('Electrical & Wiring', 'electrical', 'fa-bolt', 'Wiring, cables, switches, conduits, and electrical accessories', 4),
('Paints & Finishes', 'paints', 'fa-paint-roller', 'Interior and exterior paints, primers, and surface finishes', 5),
('Plumbing & Hardware', 'plumbing', 'fa-wrench', 'Pipes, fittings, valves, sanitary ware, and general hardware', 6);

-- Products — Building Materials
INSERT INTO products (category_id, name, description, specifications, price, unit, in_stock, featured) VALUES
(1, '5kg Bag of Cement', 'High-strength Portland cement suitable for all construction work', 'Brand: Bamburi / Savannah / National | Grade: 42.5N | Qty: 5kg', 65.00, 'bag', 1, 1),
(1, '50kg Bag of Cement', 'Full-size Portland cement bag for major construction projects', 'Brand: Bamburi / Savannah | Grade: 42.5N | Qty: 50kg', 640.00, 'bag', 1, 0),
(1, 'Concrete Blocks (6")', 'Machine-pressed hollow concrete blocks for wall construction', 'Size: 6" x 8" x 16" | Strength: High density', 35.00, 'piece', 1, 0),
(1, 'River Sand', 'Clean washed river sand for plastering and concrete mixing', 'Grade: Fine/Course | Delivery: Per trip', 4500.00, 'trip', 1, 0),
(1, 'Ballast / Hardcore', 'Crushed stone aggregate for concrete foundations and slabs', 'Size: 19mm aggregate | Per trip delivery', 5500.00, 'trip', 1, 0),
(1, 'Building Timber (2x4)', 'Treated hardwood timber planks for formwork and roofing', 'Size: 2" x 4" x 12ft | Treatment: CCA treated', 380.00, 'piece', 1, 0);

-- Products — Structural Steel
INSERT INTO products (category_id, name, description, specifications, price, unit, in_stock, featured) VALUES
(2, 'Y12 Steel Rebar (12m)', 'High-yield deformed steel bar for reinforced concrete', 'Diameter: 12mm | Length: 12m | Grade: Y/BRC', 580.00, 'bar', 1, 1),
(2, 'Y16 Steel Rebar (12m)', 'Heavy-duty reinforcement bar for slabs and columns', 'Diameter: 16mm | Length: 12m | Grade: Y', 980.00, 'bar', 1, 0),
(2, 'Y10 Steel Rebar (12m)', 'Standard reinforcement bar for footings and walls', 'Diameter: 10mm | Length: 12m | Grade: Y', 420.00, 'bar', 1, 0),
(2, 'Structural Steel (2"x2" Box)', 'Square hollow section steel for frames and structures', 'Size: 2" x 2" x 2mm | Length: 6m', 1200.00, 'piece', 1, 1),
(2, 'Steel Angle Iron (2"x2")', 'Equal leg angle section for bracing and frames', 'Size: 50x50x5mm | Length: 6m', 850.00, 'piece', 1, 0),
(2, 'Flat Bar Steel (3"x6mm)', 'Flat steel bar for fabrication and general metalwork', 'Size: 75mm x 6mm | Length: 6m', 640.00, 'piece', 0, 0);

-- Products — Tools
INSERT INTO products (category_id, name, description, specifications, price, unit, in_stock, featured) VALUES
(3, 'Grocer Tools Set (10pcs)', 'Essential hand tool set for builders and contractors', '10-piece set: hammer, pliers, screwdrivers, tape measure', 1800.00, 'set', 1, 0),
(3, 'Hand Saw (24")', 'Quality crosscut hand saw for timber cutting', 'Blade length: 24" | Hardened teeth | Rubber handle', 450.00, 'piece', 1, 0),
(3, 'Shoerhead (Round Nose Shovel)', 'Heavy-duty round nose shovel for digging and excavation', 'Blade: Carbon steel | Handle: Hardwood, 1.2m', 580.00, 'piece', 1, 0),
(3, 'Breaker Gonoset', 'Chisel and hammer set for breaking concrete and masonry', 'Set: Cold chisel + Lump hammer', 750.00, 'set', 1, 0),
(3, 'Blummed Steel (Level)', 'Spirit level for accurate alignment and leveling', 'Size: 24" (600mm) | 3 bubble vials | Aluminium', 650.00, 'piece', 1, 0),
(3, 'Samter Yeast (Wheel Barrow)', 'Heavy-duty contractor grade wheelbarrow', 'Capacity: 150L | Steel body | Pneumatic tyre', 4500.00, 'piece', 1, 0);

-- Products — Electrical
INSERT INTO products (category_id, name, description, specifications, price, unit, in_stock, featured) VALUES
(4, 'Electrical Wire 2.5mm (100m)', '2.5mm single core PVC insulated copper wire', 'Core: 2.5mm² | Length: 100m roll | Color: Red/Black/Green', 2800.00, 'roll', 1, 1),
(4, 'Electrical Wire 1.5mm (100m)', '1.5mm single core PVC insulated copper wire', 'Core: 1.5mm² | Length: 100m roll | PVC insulated', 1800.00, 'roll', 1, 0),
(4, 'Conduit Pipe (20mm)', 'PVC electrical conduit pipe for wire protection', 'Diameter: 20mm | Length: 3m | PVC material', 85.00, 'piece', 1, 0),
(4, 'MCB Circuit Breaker (20A)', 'Miniature circuit breaker for electrical panel protection', 'Rating: 20A | Single pole | Breaking capacity: 6kA', 280.00, 'piece', 1, 0),
(4, 'Single Socket Outlet', 'Surface mount single power socket with safety shutters', '13A rated | White finish | Safety shutters', 120.00, 'piece', 1, 0),
(4, 'Fluorescent Tube 36W', 'Energy efficient fluorescent tube for workshop/warehouse lighting', 'Power: 36W | Base: G13 | Life: 8000hrs | Length: 1200mm', 150.00, 'piece', 1, 0);

-- Products — Paints
INSERT INTO products (category_id, name, description, specifications, price, unit, in_stock, featured) VALUES
(5, 'Gocoat Paint (White 4L)', 'Premium interior/exterior emulsion paint', 'Size: 4L | Finish: Matt | Coverage: 12-14 m²/L', 850.00, 'tin', 1, 1),
(5, 'Minkabo Elrara (Primer)', 'Alkali-resistant masonry primer for new plaster', 'Size: 4L | Type: Masonry primer | One coat coverage', 720.00, 'tin', 1, 0),
(5, 'Garcoet Font (5L)', 'Water-based gloss paint for woodwork and metal', 'Size: 5L | Finish: Gloss | Base: Water', 1100.00, 'tin', 1, 0),
(5, 'Grader Arlea (Varnish)', 'Clear polyurethane varnish for wood protection', 'Size: 1L | Finish: Satin/Gloss | Interior/Exterior', 480.00, 'tin', 0, 0),
(5, 'Gomoled Elrara (5L)', 'Heavy duty floor paint for concrete floors', 'Size: 5L | Type: Epoxy floor paint | Colours available', 1800.00, 'tin', 1, 0),
(5, 'Gomoled Kaant (20L)', 'Bulk exterior masonry paint — contractor pack', 'Size: 20L | Finish: Matt | Weather-resistant', 3200.00, 'tin', 1, 0);

-- Products — Plumbing
INSERT INTO products (category_id, name, description, specifications, price, unit, in_stock, featured) VALUES
(6, 'PVC Pipe 1/2" (6m)', 'uPVC pressure pipe for cold water supply systems', 'Size: ½" (15mm) | Length: 6m | Pressure: 10 bar', 185.00, 'piece', 1, 0),
(6, 'GI Pipe 1" (6m)', 'Galvanised iron pipe for water and gas supply', 'Size: 1" (25mm) | Length: 6m | BS standard', 1200.00, 'piece', 1, 0),
(6, 'Ball Valve 1/2"', 'Full bore ball valve for water control', 'Size: ½" | Type: Full bore | Brass body', 250.00, 'piece', 1, 0),
(6, 'Flex Hose Connector', 'Flexible stainless steel braided hose for tap connections', 'Length: 300mm | Ends: 1/2" BSP | Stainless braid', 180.00, 'piece', 1, 0),
(6, 'Nails Assorted (1kg)', 'Round wire nails — builder grade, assorted sizes', '1kg mixed bag: 2", 3", 4" round wire nails', 95.00, 'kg', 1, 0),
(6, 'Plasm Goors (Polypipe 2")', 'Polypropylene drainage pipe for waste water systems', 'Size: 2" (50mm) | Length: 3m | Solvent weld system', 320.00, 'piece', 1, 0);
