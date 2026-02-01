# Complete Fix for Image-Based PDF Resumes

## The Problem

Your logs clearly show:
- ✅ PDF file exists (46KB)
- ✅ smalot/pdfparser reads the file
- ❌ **But extracts ZERO text** (`content_length: 0`)

This happens when applicants upload **scanned/image-based PDFs** instead of text-based PDFs.

---

## Solution Options (Choose Based on Your Access Level)

### 🏆 **BEST: Option 1 - Install pdftotext (Fast & Reliable)**

**Requirements:** SSH access to your server

**Steps:**
```bash
# 1. SSH into your server
ssh your-username@hr1.jetlougetravels-ph.com

# 2. Install poppler-utils (contains pdftotext)
sudo apt-get update
sudo apt-get install poppler-utils -y

# 3. Verify installation
pdftotext -v
# Should show: pdftotext version 0.x.x

# 4. Test with one of your PDFs
pdftotext /home/hr1.jetlougetravels-ph.com/public_html/storage/app/public/resumes/4GcM6GUwlNMrhSDneHos4dsZZsbUUfq4H1dXeJZ5.pdf test_output.txt
cat test_output.txt
# If you see text, it works!
```

**Then:**
1. Upload the updated `app/Livewire/Website/ApplyNow.php` 
2. The code now automatically uses pdftotext as fallback
3. Test by submitting a new application

---

### 🥈 **Option 2 - Pure PHP Solution (No SSH Required)**

**Requirements:** Composer access (cPanel or FTP)

Add a better PDF library:

```bash
composer require spatie/pdf-to-text
```

Then update the code to use Spatie's library. Let me create that version for you:

<function_calls>
<invoke name="write_to_file">
<parameter name="CodeContent"><?php
/**
 * ALTERNATIVE PDF PARSING - Using Spatie PDF to Text
 * 
 * Drop this code into ApplyNow.php if you install: composer require spatie/pdf-to-text
 * 
 * Replace the PDF parsing section (around line 252) with this:
 */

// Try multiple PDF parsing methods
$resumeContent = null;

// Method 1: Try smalot/pdfparser first
try {
    if (class_exists('\Smalot\PdfParser\Parser')) {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);
        $resumeContent = $pdf->getText();
        
        \Log::info("Attempted smalot/pdfparser", [
            'application_id' => $application->id,
            'content_length' => strlen($resumeContent),
        ]);
    }
} catch (\Exception $e) {
    \Log::warning("smalot/pdfparser failed: " . $e->getMessage());
}

// Method 2: If smalot returns empty, try Spatie PDF to Text
if (empty(trim($resumeContent)) && class_exists('\Spatie\PdfToText\Pdf')) {
    try {
        $text = \Spatie\PdfToText\Pdf::getText($filePath);
        
        if (!empty(trim($text))) {
            $resumeContent = $text;
            \Log::info("PDF extracted using Spatie PDF-to-Text", [
                'application_id' => $application->id,
                'content_length' => strlen($resumeContent),
            ]);
        }
    } catch (\Exception $e) {
        \Log::warning("Spatie PDF-to-Text failed: " . $e->getMessage());
    }
}

// Method 3: Final fallback - try pdftotext command
if (empty(trim($resumeContent))) {
    try {
        $outputFile = storage_path('app/temp_pdf_' . $application->id . '.txt');
        $command = "pdftotext " . escapeshellarg($filePath) . " " . escapeshellarg($outputFile);
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($outputFile)) {
            $resumeContent = file_get_contents($outputFile);
            @unlink($outputFile);
            
            \Log::info("PDF extracted using pdftotext command", [
                'application_id' => $application->id,
                'content_length' => strlen($resumeContent),
            ]);
        }
    } catch (\Exception $e) {
        \Log::warning("pdftotext command failed: " . $e->getMessage());
    }
}

// If still empty, log detailed error
if (empty(trim($resumeContent))) {
    \Log::error("ALL PDF extraction methods failed - PDF is likely image-based without text layer", [
        'application_id' => $application->id,
        'file_path' => $filePath,
        'file_size' => filesize($filePath),
        'file_readable' => is_readable($filePath),
        'recommendation' => 'User should upload text-based PDF or you need to install OCR (Tesseract)',
    ]);
}
