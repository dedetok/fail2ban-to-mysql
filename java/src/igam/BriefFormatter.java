package igam;

import java.util.logging.LogRecord;

class BriefFormatter extends java.util.logging.Formatter 
{   
	private static final java.text.DateFormat dateFormat = new java.text.SimpleDateFormat("yyyy-MM-dd hh:mm:ss.SSS"); 
	
    public BriefFormatter() { super(); }

    @Override 
    public String format(final LogRecord record) 
    {
        //return record.getMessage()+"\n";
        StringBuilder sb = new StringBuilder();
        java.util.Calendar cal = java.util.Calendar.getInstance();
        sb.append(dateFormat.format(cal.getTime()));
        sb.append("\t");
        sb.append("[").append(record.getLevel()).append("]\t");
        sb.append("class:[").append(record.getSourceClassName()).append("]\t");
        if (null != record.getThrown()) {
            sb.append("Throwable occurred: "); //$NON-NLS-1$
            Throwable t = record.getThrown();
            java.io.PrintWriter pw = null;
            try {
            	java.io.StringWriter sw = new java.io.StringWriter();
                pw = new java.io.PrintWriter(sw);
                t.printStackTrace(pw);
                sb.append(sw.toString());
                sb.append("\t");
            } finally {
                if (pw != null) {
                    try {
                        pw.close();
                    } catch (Exception e) {
                        // ignore
                    }
                }
            }
        } else {
            sb.append(formatMessage(record));
        }
        sb.append(System.getProperty("line.separator"));
        return sb.toString();
    }

}
