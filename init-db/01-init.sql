-- Initialize contacts database
CREATE TABLE IF NOT EXISTS contacts (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create index on email for faster lookups
CREATE INDEX idx_contacts_email ON contacts(email);

-- Insert some sample data
INSERT INTO contacts (first_name, last_name, email, phone) VALUES
    ('John', 'Doe', 'john.doe@example.com', '555-0101'),
    ('Jane', 'Smith', 'jane.smith@example.com', '555-0102'),
    ('Bob', 'Johnson', 'bob.johnson@example.com', '555-0103')
ON CONFLICT (email) DO NOTHING;
