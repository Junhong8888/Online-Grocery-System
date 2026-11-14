
Week 2: Infrastructure Setup & Authentication/Registration Core
Joint Team Task: Finalize project folder structure (/auth, /shop, /cart, /admin), write the shared PDO database connection (db.php), and execute the base SQL script in PHPMyAdmin.

Member 1 (Security & Profile):
    [ ] Build the secure login and logout page logic.
    [ ] Implement secure password hashing (password_hash() and password_verify()).   
Member 2 (Product Catalog & Admin):
    [ ] Set up the product catalog folder structure and base layout templates.
    [ ] Begin populating initial grocery sample data (categories and sample items) in the database.   
Member 3 (Cart, Checkout & Orders):
    [ ] Set up session initialization utilities for shopping cart data storage.
    [ ] Draft the cart interface layout.
Member 4 (Member Maintenance & Security):
    [ ] Build the 
Member Registration form and server-side input validation script.
    [ ] Implement base session role checks (
    Member vs. Admin).

Week 3: Core Modules (Catalog, Profiles & Registration)
Joint Team Task: Review initial database integration and test cross-page session variables.

Member 1 (Security & Profile):
    [ ] Implement the Password Reset module.
    [ ] Build the Profile Photo Upload feature (with file type/size server-side validation).
Member 2 (Product Catalog & Admin):
    [ ] Develop the Product Listing and Product Detail views for grocery items.   
    [ ] Implement Basic Searching functionality for products.   
Member 3 (Cart, Checkout & Orders):
    [ ] Implement core Shopping Cart logic (add item to cart, update quantity, remove item).
Member 4 (Member Maintenance & Security):
    [ ] Complete Member Listing and Member Detail views (Admin side).   
    [ ] Implement Admin Search for members.   

Week 4: Product CRUD, Checkout & Admin Dashboards
Joint Team Task: Mid-project integration check—verify that product items from Member 2's catalog can be added successfully to Member 3's shopping cart.

Member 1 (Security & Profile):
    [ ] Build the User Profile Update module (name, email, phone).   
    [ ] Build the Password Update module for logged-in users.   
Member 2 (Product Catalog & Admin):
    [ ] Build Product CRUD operations (Create, Read, Update, Delete) for Admin.
    [ ] Implement Product Photo Upload for admin management.
Member 3 (Cart, Checkout & Orders):
    [ ] Build the Checkout workflow and Order Creation logic (transferring cart items to orders and order_items tables).
Member 4 (Member Maintenance & Security):
    [ ] Audit all member-related forms to ensure robust server-side security           
        validations and error messages.
    [ ] Implement additional security checks for restricted admin pages.

Week 5: Orders Tracking, Additional Modules & Self-Study Enhancements (CLO3)
Joint Team Task: Audit all forms across the entire system to ensure strict server-side validation and consistent error handling.

Member 1 (Security & Profile):
    [ ] Implement additional self-study feature (e.g., Temporary Login Blocking after 
        3 failed attempts or Remember Me session persistence).   
Member 2 (Product Catalog & Admin):
    [ ] Implement grocery-specific self-study features (e.g., Product Filtering by 
        category/price range or Multiple Photo Uploads).
Member 3 (Cart, Checkout & Orders):
    [ ] Build Member Order History and Order Detail pages.
    [ ] Build Admin Order Listing and Order Detail management dashboards.
Member 4 (Member Maintenance & Security):
    [ ] Implement admin self-study features (e.g., Block/Unblock User Account or Admin 
        Activity Logs).
    
Week 6: End-to-End Testing, Code Audit & Slide Deck Compilation
Joint Team Task: Execute a full end-to-end simulation of the user journey (Registration -> Login -> Browse Groceries -> Cart -> Checkout -> Admin Order Review).

Member 1 & Member 2:
    [ ] Export the final database schema ERD directly using PHPMyAdmin Designer.
    [ ] Verify code compliance against course conventions (no forbidden frameworks, 
        proper jQuery usage, custom HTML helpers).

Member 3 & Member 4:
    [ ] Capture clean screenshots of all implemented pages with clear titles for the 
        presentation slide deck.   
    [ ] Compile individual member sub-cover pages detailing respective modules 
        handled.   

All Members:
    [ ] Finalize the PowerPoint/Google Slides presentation deck in PDF format.
    [ ] Export the final MySQL database (.sql) and package the clean PHP project 
        folder into a .zip archive.

Week 7: Final Submission & Project Demonstration
Joint Team Task:
    [ ] Submit the final deliverables (Slide PDF, Project ZIP, SQL database export) before the deadline.   
    [ ] Participate in the live project demonstration and technical Q&A session, ensuring every member can explain their respective code modules