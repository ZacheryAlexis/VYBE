<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <h2 style="color: var(--vybe-orange); margin-top: 0;">Add New Category</h2>
    <p style="color: var(--vybe-muted); margin-bottom: 25px;">Enter the details for the new category below.</p>
    
    <form name="newcategory" action="index.php" method="post">
        <div style="display: grid; grid-template-columns: 140px 1fr; gap: 15px; align-items: center; margin-bottom: 25px;">
            <label style="color: var(--vybe-text); font-weight: 500;">Category ID:</label>
            <input type="number" name="categoryID" min="100" max="999" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
            
            <label style="color: var(--vybe-text); font-weight: 500;">Category Code:</label>
            <input type="text" name="categoryCode" placeholder="XXX" minlength="3" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
            
            <label style="color: var(--vybe-text); font-weight: 500;">Category Name:</label>
            <input type="text" name="categoryName" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
        </div>
        
        <div style="display: flex; gap: 10px;">
            <input type="submit" value="Add Category" 
                   style="background: var(--vybe-orange); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
            <a href="index.php?content=listcategories" class="btn-ghost" 
               style="display: inline-block; padding: 12px 30px; text-decoration: none; font-size: 1.1rem; font-weight: 600;">Cancel</a>
        </div>
        <input type="hidden" name="content" value="addcategory">
    </form>
</div>
