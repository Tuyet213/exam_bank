const fs = require('fs');
const path = require('path');

// Các thư mục cần xử lý
const directories = [
  'Pages/Admin',
  'Pages/TBM',
  'Pages/TK',
  'Pages/QualityOffice'
];

// Mapping giữa Layout cũ và role tương ứng trong AppLayout
const layoutMap = {
  'AdminLayout': 'admin',
  'TBMLayout': 'tbm',
  'TKLayout': 'tk',
  'QualityLayout': 'dbcl'
};

// Hàm đệ quy để xử lý tất cả các file trong một thư mục
function processDirectory(directory) {
  const items = fs.readdirSync(directory);
  
  for (const item of items) {
    const fullPath = path.join(directory, item);
    const stat = fs.statSync(fullPath);
    
    if (stat.isDirectory()) {
      processDirectory(fullPath);
    } else if (item.endsWith('.vue')) {
      processVueFile(fullPath);
    }
  }
}

// Hàm xử lý file Vue
function processVueFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  let changed = false;
  
  // Kiểm tra và thay thế import statement
  for (const oldLayout in layoutMap) {
    const importRegex = new RegExp(`import\\s+${oldLayout}\\s+from\\s+["'][@/]Layouts/${oldLayout}\\.vue["']`, 'g');
    
    if (importRegex.test(content)) {
      content = content.replace(importRegex, `import AppLayout from "@/Layouts/AppLayout.vue"`);
      
      // Thay thế các thẻ Layout
      const openTagRegex = new RegExp(`<${oldLayout}[^>]*>`, 'g');
      const closeTagRegex = new RegExp(`</${oldLayout}>`, 'g');
      
      content = content.replace(openTagRegex, `<AppLayout role="${layoutMap[oldLayout]}">`);
      content = content.replace(closeTagRegex, '</AppLayout>');
      
      changed = true;
    }
  }
  
  if (changed) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated: ${filePath}`);
  }
}

// Xử lý tất cả các thư mục
for (const dir of directories) {
  const fullPath = path.join(__dirname, dir);
  if (fs.existsSync(fullPath)) {
    processDirectory(fullPath);
  } else {
    console.log(`Directory not found: ${fullPath}`);
  }
}

console.log('Layout update completed!'); 