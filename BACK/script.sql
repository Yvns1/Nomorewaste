CREATE DATABASE IF NOT EXISTS GestionDeDechets;
USE GestionDeDechets;

-- Table 'Utilisateurs'
CREATE TABLE IF NOT EXISTS Utilisateurs (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'commerçant', 'membre', 'bénévole') DEFAULT NULL,
    additional_info TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    token VARCHAR(255) DEFAULT NULL
);

-- Table 'Commercants'
CREATE TABLE IF NOT EXISTS Commercants (
    merchant_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    additional_info TEXT,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES Utilisateurs(user_id)
);

-- Table 'Membres'
CREATE TABLE IF NOT EXISTS Membres (
    member_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    membership_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    renewal_date DATETIME DEFAULT NULL,
    status ENUM('active', 'expired') DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES Utilisateurs(user_id)
);

-- Table 'Benevoles'
CREATE TABLE IF NOT EXISTS Benevoles (
    volunteer_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    skills TEXT,
    availability TEXT,
    assigned_tasks TEXT,
    FOREIGN KEY (user_id) REFERENCES Utilisateurs(user_id)
);

-- Table 'Produits'
CREATE TABLE IF NOT EXISTS Produits (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    expiration_date DATE DEFAULT NULL,
    quantity INT DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    barcode VARCHAR(255) DEFAULT NULL
);

-- Table 'Collectes'
CREATE TABLE IF NOT EXISTS Collectes (
    collection_id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    time TIME DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    collected_by INT DEFAULT NULL,
    status ENUM('completed', 'in_progress') DEFAULT NULL,
    FOREIGN KEY (collected_by) REFERENCES Benevoles(volunteer_id) ON DELETE SET NULL
);

-- Table 'Tours'
CREATE TABLE IF NOT EXISTS Tours (
    tour_id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    time TIME DEFAULT NULL,
    start_location VARCHAR(255) DEFAULT NULL,
    end_location VARCHAR(255) DEFAULT NULL,
    assigned_volunteers TEXT,
    status ENUM('planned', 'in_progress', 'completed') DEFAULT NULL
);

-- Table 'Services'
CREATE TABLE IF NOT EXISTS Services (
    service_id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(255) NOT NULL,
    description TEXT,
    availability VARCHAR(255) DEFAULT NULL,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES Utilisateurs(user_id)
);

-- Table 'Planifications'
CREATE TABLE IF NOT EXISTS Planifications (
    planning_id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    service_id INT DEFAULT NULL,
    volunteer_id INT DEFAULT NULL,
    status ENUM('scheduled', 'completed', 'canceled') DEFAULT NULL,
    FOREIGN KEY (service_id) REFERENCES Services(service_id),
    FOREIGN KEY (volunteer_id) REFERENCES Benevoles(volunteer_id)
);

-- Table 'Messages'
CREATE TABLE IF NOT EXISTS Messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT DEFAULT NULL,
    recipient_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES Utilisateurs(user_id),
    FOREIGN KEY (recipient_id) REFERENCES Utilisateurs(user_id)
);

-- Table 'AuditsAntiGaspillage'
CREATE TABLE IF NOT EXISTS AuditsAntiGaspillage (
    audit_id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    business_type VARCHAR(255) DEFAULT NULL,
    current_challenges TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table 'FrigosIntelligents'
CREATE TABLE IF NOT EXISTS FrigosIntelligents (
    fridge_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT DEFAULT NULL,
    quantity INT DEFAULT NULL,
    recommended_menu TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Produits(product_id)
);

-- Table 'Factures'
CREATE TABLE IF NOT EXISTS Factures (
    invoice_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('issued', 'paid', 'canceled') DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Utilisateurs(user_id)
);

-- Table 'Paiements'
CREATE TABLE IF NOT EXISTS Paiements (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('card', 'bank_transfer', 'cash') DEFAULT NULL,
    status ENUM('received', 'pending', 'canceled') DEFAULT NULL,
    FOREIGN KEY (invoice_id) REFERENCES Factures(invoice_id)
);

-- Table 'Jetons'
CREATE TABLE IF NOT EXISTS Jetons (
    token_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    token VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME DEFAULT NULL,
    type ENUM('verification', 'session', 'password_reset') DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES Utilisateurs(user_id)
);
