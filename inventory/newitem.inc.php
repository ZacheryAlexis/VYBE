<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <h2 style="color: var(--vybe-orange); margin-top: 0;">Add New Item</h2>
    <p style="color: var(--vybe-muted); margin-bottom: 25px;">Enter the details for the new item below.</p>
    
    <form name="newitem" action="index.php" method="post">
        <div style="display: grid; grid-template-columns: 140px 1fr; gap: 15px; align-items: center; margin-bottom: 25px;">
            <label style="color: var(--vybe-text); font-weight: 500;">Item ID:</label>
            <input type="number" name="itemID" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
            
            <label style="color: var(--vybe-text); font-weight: 500;">Name:</label>
            <input type="text" name="itemName" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
            
            <label style="color: var(--vybe-text); font-weight: 500;">Category ID:</label>
            <input type="number" name="categoryID" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
            
            <label style="color: var(--vybe-text); font-weight: 500;">List Price:</label>
            <input type="number" name="listPrice" step="0.01" required 
                   style="padding: 10px; background: var(--vybe-bg); color: var(--vybe-text); border: 1px solid var(--vybe-navy); border-radius: 6px; font-size: 1rem;">
        </div>
        
        <div style="display: flex; gap: 10px;">
            <input type="submit" value="Add Item" 
                   style="background: var(--vybe-orange); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
            <a href="index.php?content=listitems" class="btn-ghost" 
               style="display: inline-block; padding: 12px 30px; text-decoration: none; font-size: 1.1rem; font-weight: 600;">Cancel</a>
        </div>
        <input type="hidden" name="content" value="additem">
    </form>
</div>
