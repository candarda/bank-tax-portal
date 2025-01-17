import { sql } from '@vercel/postgres';
import { NextResponse } from 'next/server';

export async function POST(request: Request) {
  try {
    const {
      first_name,
      last_name,
      email,
      phone,
      password,
      street,
      house_number,
      postal_code,
      city,
      bank_id,
      birth_date
    } = await request.json();

    // Banka kontrolü
    const bankCheck = await sql`
      SELECT id FROM banks WHERE id = ${bank_id} AND active = true;
    `;

    if (bankCheck.rows.length === 0) {
      return NextResponse.json(
        { message: 'Bitte wählen Sie eine gültige Bank aus.' },
        { status: 400 }
      );
    }

    // Müşteri verilerini kaydet
    const result = await sql`
      INSERT INTO customer_data (
        first_name, last_name, email, phone, password,
        street, house_number, postal_code, city,
        bank_id, birth_date
      ) VALUES (
        ${first_name}, ${last_name}, ${email}, ${phone}, ${password},
        ${street}, ${house_number}, ${postal_code}, ${city},
        ${bank_id}, ${birth_date}
      ) RETURNING id;
    `;

    return NextResponse.json({
      message: 'Vielen Dank! Ihre Informationen wurden erfolgreich gespeichert.',
      id: result.rows[0].id
    });

  } catch (error) {
    console.error('Form submission error:', error);
    return NextResponse.json(
      { message: 'Ein Fehler ist aufgetreten' },
      { status: 500 }
    );
  }
} 