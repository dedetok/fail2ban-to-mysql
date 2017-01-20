package igam;

import java.net.UnknownHostException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.Statement;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.logging.Level;

class IPFromMySQL {
	Connection conn=null;
	
	ArrayList<String> mSSH = new ArrayList<String>();
	ArrayList<String> mFTP = new ArrayList<String>();
	ArrayList<String> mHTTP = new ArrayList<String>();
	ArrayList<String> mSMTP = new ArrayList<String>();
	
    String regex = "\\A(25[0-5]|2[0-4]\\d|[0-1]?\\d?\\d)";
	
	
/**
 * Constructor
 * | 10 | SSH                  |
 * | 20 | FTP                  |
 * | 30 | HTTP/HTTPS           |
 * | 40 | SMTP/POP/IMAP/POP3/S | 	
 * @throws ClassNotFoundException
 * @throws SQLException
 * @throws UnknownHostException 
 */
	public IPFromMySQL() throws ClassNotFoundException, SQLException, UnknownHostException {
		BlockIP.myLog.log(Level.INFO, "Creating IPFromMySQL instance to Read MySQL");

		Class.forName("com.mysql.jdbc.Driver");
    // database name myf2b, user = user, password = password
		conn = DriverManager.getConnection("jdbc:mysql://localhost/myf2b?user=user&password=password");

        //String query = "select * from kci_logipv4 where kci_category = 10 and date_sub(curdate(), interval 1 day) <= logdate;"; // only SSH (10)
        String query = "select * from kci_logipv4 where and date_sub(curdate(), interval 1 day) <= logdate;";
		Statement stmt = conn.createStatement();
		ResultSet rs = stmt.executeQuery(query);
		while (rs.next()) {
			long ipv4 = rs.getLong("logipv4");
			int category = rs.getInt("kci_category");
            switch (category) {
                case 10: 
                    String tmp = long2ip(ipv4);
                    mSSH.add(tmp);
                    break;
                case 20:
                    mFTP.add(long2ip(ipv4));
                    break;
                case 30:
                    mHTTP.add(long2ip(ipv4));
                    break;
                case 40:
                    mSMTP.add(long2ip(ipv4));
                    break;
            } 
		}
		
		mSSH = filterIPNetmask(mSSH);
		mFTP = filterIPNetmask(mFTP);
		mHTTP = filterIPNetmask(mHTTP);
		mSMTP = filterIPNetmask(mSMTP);

	}
	
	/**
	 * Converting long to IPv4 address
	 * @param ip long 
	 * @return IPv4 address
	 * @throws java.net.UnknownHostException
	 */
	String long2ip(long ip) throws java.net.UnknownHostException {
		byte[] bytes = new byte[4];
		    
		bytes[0] = (byte) ((ip & 0xff000000) >> 24);
		bytes[1] = (byte) ((ip & 0x00ff0000) >> 16);
		bytes[2] = (byte) ((ip & 0x0000ff00) >> 8);
		bytes[3] = (byte) (ip & 0x000000ff);
		    
		return java.net.InetAddress.getByAddress(bytes).getHostAddress();
	}
	
	/**
	 * Find IP with similiar netmask /24
	 * @param ArrayList<String> mArray
	 * @return ArrayList<String> mArrayClean 
	 */
	ArrayList<String> filterIPNetmask(ArrayList<String> mArray){
		ArrayList<String> mArrayClean = new ArrayList<String>();
		String[] mTempNew = mArray.toArray(new String[0]);
		String[] mTempNew2 = mTempNew.clone();
		for (int i=0;i<mTempNew.length;i++) { 
			boolean isIPExist = false;
			String[] newIP = mTempNew[i].split("[.]");
	    	String subNewIP="";
	    	// 255.255.255.
        	for (int j=0;j<3;j++) {
        		subNewIP=subNewIP+newIP[j]+".";
        	}
        	// compare mTempNew with mTempNew2
        	// if exist, there are some IP with same netmask
        	for (int j=0;j<mTempNew2.length;j++) {
        		if (mTempNew2[j].indexOf(subNewIP)>=0) {
        			// skip compare with the equal IP
        			if (!mTempNew2[j].equalsIgnoreCase(mTempNew[i])) {
        				isIPExist = true;
        				break;
        			}
        		}
        	}
        	if (isIPExist) {
        		subNewIP=subNewIP+"0/24";
        		String[] mClean = mArrayClean.toArray(new String[0]);
        		boolean isAdded = true;
        		for (int k=0;k<mClean.length;k++) {
        			if (mClean[k].indexOf(subNewIP)>=0) {
        				isAdded = false;
        				break;
        			}
        		}
        		if (isAdded) {
        			mArrayClean.add(subNewIP);
        		}
        	} else {
        		mArrayClean.add(mTempNew[i]);
        	}
        }
		return mArrayClean;
	}
}
