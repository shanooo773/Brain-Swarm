"""
Simple database layer for authentication system
Uses SQLite for simplicity and portability
"""
import sqlite3
import hashlib
import secrets
from datetime import datetime
from typing import Optional, List, Dict, Any
from contextlib import contextmanager


class Database:
    def __init__(self, db_path: str = "auth_backend.db"):
        self.db_path = db_path
        self.init_database()
    
    def init_database(self):
        """Initialize database with required tables"""
        with self.get_connection() as conn:
            # Users table
            conn.execute("""
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    password_hash TEXT NOT NULL,
                    full_name TEXT,
                    role TEXT DEFAULT 'user',
                    is_active BOOLEAN DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            """)
            
            # Bookings table
            conn.execute("""
                CREATE TABLE IF NOT EXISTS bookings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    service_type TEXT NOT NULL,
                    preferred_date TEXT NOT NULL,
                    message TEXT,
                    phone TEXT,
                    status TEXT DEFAULT 'pending',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users (id)
                )
            """)
            
            # Create default admin user if not exists
            admin_exists = conn.execute(
                "SELECT COUNT(*) FROM users WHERE username = 'admin'"
            ).fetchone()[0]
            
            if admin_exists == 0:
                admin_password = self.hash_password("admin123")
                conn.execute("""
                    INSERT INTO users (username, email, password_hash, full_name, role)
                    VALUES (?, ?, ?, ?, ?)
                """, ("admin", "admin@brainswarm.com", admin_password, "Administrator", "admin"))
    
    @contextmanager
    def get_connection(self):
        """Get database connection with context management"""
        conn = sqlite3.connect(self.db_path)
        conn.row_factory = sqlite3.Row  # Enable dict-like access
        try:
            yield conn
            conn.commit()
        except Exception:
            conn.rollback()
            raise
        finally:
            conn.close()
    
    def hash_password(self, password: str) -> str:
        """Hash password with salt"""
        salt = secrets.token_hex(16)
        password_hash = hashlib.pbkdf2_hmac('sha256', password.encode(), salt.encode(), 100000)
        return f"{salt}:{password_hash.hex()}"
    
    def verify_password(self, password: str, password_hash: str) -> bool:
        """Verify password against hash"""
        try:
            salt, hash_part = password_hash.split(':')
            password_check = hashlib.pbkdf2_hmac('sha256', password.encode(), salt.encode(), 100000)
            return password_check.hex() == hash_part
        except ValueError:
            return False
    
    def create_user(self, username: str, email: str, password: str, full_name: Optional[str] = None) -> Dict[str, Any]:
        """Create a new user"""
        password_hash = self.hash_password(password)
        
        with self.get_connection() as conn:
            cursor = conn.execute("""
                INSERT INTO users (username, email, password_hash, full_name)
                VALUES (?, ?, ?, ?)
            """, (username, email, password_hash, full_name))
            
            user_id = cursor.lastrowid
            
            # Return the created user immediately
            row = conn.execute(
                "SELECT * FROM users WHERE id = ?", (user_id,)
            ).fetchone()
            return dict(row) if row else None
    
    def get_user_by_username(self, username: str) -> Optional[Dict[str, Any]]:
        """Get user by username"""
        with self.get_connection() as conn:
            row = conn.execute(
                "SELECT * FROM users WHERE username = ?", (username,)
            ).fetchone()
            return dict(row) if row else None
    
    def get_user_by_id(self, user_id: int) -> Optional[Dict[str, Any]]:
        """Get user by ID"""
        with self.get_connection() as conn:
            row = conn.execute(
                "SELECT * FROM users WHERE id = ?", (user_id,)
            ).fetchone()
            return dict(row) if row else None
    
    def authenticate_user(self, username: str, password: str) -> Optional[Dict[str, Any]]:
        """Authenticate user by username and password"""
        user = self.get_user_by_username(username)
        if user and self.verify_password(password, user['password_hash']):
            return user
        return None
    
    def create_booking(self, user_id: int, service_type: str, preferred_date: str, 
                      message: Optional[str] = None, phone: Optional[str] = None) -> Dict[str, Any]:
        """Create a new booking"""
        with self.get_connection() as conn:
            cursor = conn.execute("""
                INSERT INTO bookings (user_id, service_type, preferred_date, message, phone)
                VALUES (?, ?, ?, ?, ?)
            """, (user_id, service_type, preferred_date, message, phone))
            
            booking_id = cursor.lastrowid
            
            # Return the created booking immediately
            row = conn.execute(
                "SELECT * FROM bookings WHERE id = ?", (booking_id,)
            ).fetchone()
            return dict(row) if row else None
    
    def get_booking_by_id(self, booking_id: int) -> Optional[Dict[str, Any]]:
        """Get booking by ID"""
        with self.get_connection() as conn:
            row = conn.execute(
                "SELECT * FROM bookings WHERE id = ?", (booking_id,)
            ).fetchone()
            return dict(row) if row else None
    
    def get_user_bookings(self, user_id: int) -> List[Dict[str, Any]]:
        """Get all bookings for a user"""
        with self.get_connection() as conn:
            rows = conn.execute(
                "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC", (user_id,)
            ).fetchall()
            return [dict(row) for row in rows]
    
    def get_admin_stats(self) -> Dict[str, Any]:
        """Get admin dashboard statistics"""
        with self.get_connection() as conn:
            # Total users
            total_users = conn.execute("SELECT COUNT(*) FROM users").fetchone()[0]
            
            # Total bookings
            total_bookings = conn.execute("SELECT COUNT(*) FROM bookings").fetchone()[0]
            
            # Recent bookings (last 10)
            recent_bookings_rows = conn.execute("""
                SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10
            """).fetchall()
            recent_bookings = [dict(row) for row in recent_bookings_rows]
            
            # Recent users (last 10)
            recent_users_rows = conn.execute("""
                SELECT * FROM users ORDER BY created_at DESC LIMIT 10
            """).fetchall()
            recent_users = [dict(row) for row in recent_users_rows]
            
            return {
                "total_users": total_users,
                "total_bookings": total_bookings,
                "recent_bookings": recent_bookings,
                "recent_users": recent_users
            }