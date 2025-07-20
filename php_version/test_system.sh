#!/bin/bash

# Brain Swarm PHP Blog System - Comprehensive Test Script
# This script verifies all components are working correctly

echo "=========================================="
echo "Brain Swarm PHP Blog System Test"
echo "=========================================="

cd "$(dirname "$0")"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counters
PASSED=0
FAILED=0

# Function to run tests
test_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASS${NC}: $2"
        ((PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}: $2"
        ((FAILED++))
    fi
}

echo "1. Testing PHP Syntax..."
php_syntax_errors=$(find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors")
if [ -z "$php_syntax_errors" ]; then
    test_result 0 "All PHP files have correct syntax"
else
    test_result 1 "PHP syntax errors found"
    echo "$php_syntax_errors"
fi

echo -e "\n2. Testing File Structure..."
required_files=(
    "includes/config.php"
    "includes/functions.php"
    "blog/list.php"
    "blog/detail.php"
    "blog/read.php"
    "sign-in.php"
    "sign-up.php"
    "admin/index.php"
    "uploads/.htaccess"
    "SETUP_GUIDE.md"
    "TROUBLESHOOTING.md"
)

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        test_result 0 "File exists: $file"
    else
        test_result 1 "Missing file: $file"
    fi
done

echo -e "\n3. Testing Directories..."
required_dirs=(
    "uploads"
    "uploads/blog_images"
    "uploads/profile_pics"
    "static"
    "templates"
    "admin"
    "blog"
)

for dir in "${required_dirs[@]}"; do
    if [ -d "$dir" ]; then
        if [ -w "$dir" ]; then
            test_result 0 "Directory writable: $dir"
        else
            test_result 1 "Directory not writable: $dir"
        fi
    else
        test_result 1 "Missing directory: $dir"
    fi
done

echo -e "\n4. Testing .htaccess Configuration..."
if [ -f ".htaccess" ]; then
    if grep -q "RewriteRule.*blog.*detail.php" .htaccess; then
        test_result 0 "Blog URL rewrite rules present"
    else
        test_result 1 "Blog URL rewrite rules missing"
    fi
    
    if grep -q "RewriteEngine" .htaccess; then
        test_result 0 ".htaccess mod_rewrite configuration found"
    else
        test_result 1 ".htaccess mod_rewrite configuration missing"
    fi
else
    test_result 1 ".htaccess file missing"
fi

echo -e "\n5. Testing Database Schema..."
if [ -f "../db.sql" ]; then
    if grep -q "CREATE TABLE.*users" ../db.sql; then
        test_result 0 "Database schema contains users table"
    else
        test_result 1 "Database schema missing users table"
    fi
    
    if grep -q -A 1 "INSERT INTO users.*admin\|VALUES.*admin" ../db.sql; then
        test_result 0 "Default admin user in schema"
    else
        test_result 1 "Default admin user missing from schema"
    fi
else
    test_result 1 "Database schema file missing"
fi

echo -e "\n6. Testing PHP Configuration Loading..."
config_test=$(php -r "
try {
    require_once 'includes/config.php';
    echo 'CONFIG_OK';
} catch (Exception \$e) {
    echo 'CONFIG_ERROR: ' . \$e->getMessage();
}
" 2>/dev/null)

if [[ "$config_test" == "CONFIG_OK" ]]; then
    test_result 0 "Configuration loads without errors"
else
    test_result 1 "Configuration loading failed: $config_test"
fi

echo -e "\n7. Testing Functions Loading..."
functions_test=$(php -r "
error_reporting(0);
\$output = '';
ob_start();
try {
    require_once 'includes/functions.php';
    \$output = 'FUNCTIONS_OK';
} catch (Exception \$e) {
    \$output = 'FUNCTIONS_ERROR: ' . \$e->getMessage();
}
ob_end_clean();
echo \$output;
" 2>/dev/null)

if [[ "$functions_test" == "FUNCTIONS_OK" ]]; then
    test_result 0 "Functions load without errors"
else
    test_result 1 "Functions loading failed: $functions_test"
fi

echo -e "\n8. Testing Password Hashing..."
password_test=$(php -r "
require_once 'includes/functions.php';
\$hash = hashPassword('test123');
\$verify = verifyPassword('test123', \$hash);
echo \$verify ? 'HASH_OK' : 'HASH_FAIL';
" 2>/dev/null)

if [[ "$password_test" == "HASH_OK" ]]; then
    test_result 0 "Password hashing works correctly"
else
    test_result 1 "Password hashing failed"
fi

echo -e "\n9. Testing URL Functions..."
url_test=$(php -r "
\$_SERVER['HTTP_HOST'] = 'localhost';
\$_SERVER['SCRIPT_NAME'] = '/test.php';
require_once 'includes/functions.php';
\$url = smartUrl('blog/detail.php?id=1');
echo strpos(\$url, 'localhost') !== false ? 'URL_OK' : 'URL_FAIL';
" 2>/dev/null)

if [[ "$url_test" == "URL_OK" ]]; then
    test_result 0 "URL generation works correctly"
else
    test_result 1 "URL generation failed"
fi

echo -e "\n10. Testing Read.php Redirect..."
if [ -f "blog/read.php" ]; then
    if grep -q "detail.php" blog/read.php; then
        test_result 0 "read.php redirects to detail.php"
    else
        test_result 1 "read.php does not redirect properly"
    fi
else
    test_result 1 "blog/read.php file missing"
fi

echo -e "\n=========================================="
echo "Test Summary"
echo "=========================================="
echo -e "Tests Passed: ${GREEN}$PASSED${NC}"
echo -e "Tests Failed: ${RED}$FAILED${NC}"
echo -e "Total Tests: $((PASSED + FAILED))"

if [ $FAILED -eq 0 ]; then
    echo -e "\n${GREEN}🎉 ALL TESTS PASSED!${NC}"
    echo "The Brain Swarm PHP Blog System is ready for deployment."
    echo ""
    echo "Next steps:"
    echo "1. Set up MySQL database using db.sql"
    echo "2. Configure database credentials in includes/config.php"
    echo "3. Test on Apache/XAMPP environment"
    echo "4. Change default admin password: admin@brainswarm.com / password"
    exit 0
else
    echo -e "\n${YELLOW}⚠️  SOME TESTS FAILED${NC}"
    echo "Please review the failed tests above and fix any issues."
    echo "Refer to TROUBLESHOOTING.md for solutions."
    exit 1
fi