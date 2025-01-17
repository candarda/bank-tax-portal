import { sql } from '@vercel/postgres';

// Tabloları oluştur
export async function createTables() {
  try {
    // Banks tablosu
    await sql`
      CREATE TABLE IF NOT EXISTS banks (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        active BOOLEAN DEFAULT true,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `;

    // Customer data tablosu
    await sql`
      CREATE TABLE IF NOT EXISTS customer_data (
        id SERIAL PRIMARY KEY,
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        street VARCHAR(255) NOT NULL,
        house_number VARCHAR(50) NOT NULL,
        postal_code VARCHAR(10) NOT NULL,
        city VARCHAR(255) NOT NULL,
        bank_id INTEGER REFERENCES banks(id),
        birth_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
    `;

    // Varsayılan bankaları ekle
    const banks = await sql`SELECT COUNT(*) FROM banks;`;
    if (banks.rows[0].count === '0') {
      await sql`
        INSERT INTO banks (name) VALUES 
        ('Deutsche Bank'),
        ('Commerzbank'),
        ('Sparkasse'),
        ('Volksbank'),
        ('Postbank'),
        ('HypoVereinsbank'),
        ('DKB (Deutsche Kreditbank)'),
        ('ING-DiBa'),
        ('Targobank'),
        ('Santander Bank');
      `;
    }

    console.log('Tablolar başarıyla oluşturuldu');
  } catch (error) {
    console.error('Tablo oluşturma hatası:', error);
    throw error;
  }
} 